<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\DiagnosticResult;

use Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface;
use Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterfaceFactory;
use Amasty\PageSpeedOptimizer\Api\DiagnosticResultRepositoryInterface;
use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\ResourceModel\CollectionFactory;
use Magento\Framework\Data\Collection;
use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\ResourceModel\DiagnosticResult as DiagnosticResultResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class DiagnosticResultRepository implements DiagnosticResultRepositoryInterface
{
    /**
     * @var DiagnosticResultInterface
     */
    private $diagnosticResultFactory;

    /**
     * @var DiagnosticResultResource
     */
    private $diagnosticResultResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array
     */
    private $diagnosticResults;

    public function __construct(
        DiagnosticResultInterfaceFactory $diagnosticResultFactory,
        DiagnosticResultResource $diagnosticResultResource,
        CollectionFactory $collectionFactory
    ) {
        $this->diagnosticResultFactory = $diagnosticResultFactory;
        $this->diagnosticResultResource = $diagnosticResultResource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(DiagnosticResultInterface $diagnosticResult): DiagnosticResultInterface
    {
        try {
            if ($diagnosticResult->getResultId()) {
                $diagnosticResult = $this->getByResultId($diagnosticResult->getResultId())
                    ->addData($diagnosticResult->getData());
            }

            $this->diagnosticResultResource->save($diagnosticResult);
            unset($this->diagnosticResults[$diagnosticResult->getResultId()]);
        } catch (\Exception $e) {
            if ($diagnosticResult->getResultId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save diagnostic result with ID %1. Error: %2',
                        [$diagnosticResult->getResultId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new diagnostic result. Error: %1', $e->getMessage()));
        }

        return $diagnosticResult;
    }

    /**
     * @inheritdoc
     */
    public function clearAfterResultByVersion(string $version): DiagnosticResultRepositoryInterface
    {
        try {
            $connection = $this->diagnosticResultResource->getConnection();
            $connection->update(
                $this->diagnosticResultResource->getMainTable(),
                [DiagnosticResult::RESULT => new \Zend_Db_Expr('null')],
                [
                    DiagnosticResult::VERSION . ' = ?' => $version,
                    DiagnosticResult::IS_BEFORE . ' = ?' => 0
                ]
            );
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to clear diagnostic result. Error: %1', $e->getMessage()));
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getByResultId($diagnosticResultId): DiagnosticResultInterface
    {
        if (!isset($this->diagnosticResult[$diagnosticResultId])) {
            /** @var DiagnosticResultInterface $diagnosticResult */
            $diagnosticResult = $this->diagnosticResultFactory->create();
            $this->diagnosticResultResource->load($diagnosticResult, $diagnosticResultId);

            if (!$diagnosticResult->getResultId()) {
                throw new NoSuchEntityException(
                    __('Diagnostic result with specified ID "%1" not found.', $diagnosticResultId)
                );
            }
            $this->diagnosticResults[$diagnosticResultId] = $diagnosticResult;
        }

        return $this->diagnosticResults[$diagnosticResultId];
    }

    /**
     * @inheritdoc
     */
    public function getByVersionAndIsBefore(string $version, bool $isBefore): DiagnosticResultInterface
    {
        /** @var DiagnosticResultInterface $diagnosticResult */
        $diagnosticResult = $this->collectionFactory->create()
            ->addFieldToFilter(DiagnosticResult::VERSION, $version)
            ->addFieldToFilter(DiagnosticResult::IS_BEFORE, (int)$isBefore)
            ->getFirstItem();

        if (!$diagnosticResult->getResultId()) {
            throw new NoSuchEntityException(
                __('Diagnostic result with specified Version "%1" not found.', $version)
            );
        }

        return $diagnosticResult;
    }

    /**
     * @inheritdoc
     */
    public function getListResults(): array
    {
        $collection = $this->collectionFactory->create()
            ->addOrder(DiagnosticResult::VERSION, Collection::SORT_ORDER_ASC)
            ->addOrder(DiagnosticResult::IS_BEFORE, Collection::SORT_ORDER_ASC);

        return $collection->toArray()['items'];
    }
}
