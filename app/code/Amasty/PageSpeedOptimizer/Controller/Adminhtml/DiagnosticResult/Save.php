<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Controller\Adminhtml\DiagnosticResult;

use Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterfaceFactory;
use Amasty\PageSpeedOptimizer\Api\DiagnosticResultRepositoryInterface;
use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\DiagnosticResult;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Amasty_PageSpeedOptimizer::config';

    /**
     * @var DiagnosticResultInterfaceFactory
     */
    private $diagnosticResultFactory;

    /**
     * @var DiagnosticResultRepositoryInterface
     */
    private $diagnosticResultRepository;

    public function __construct(
        Context $context,
        DiagnosticResultInterfaceFactory $diagnosticResultFactory,
        DiagnosticResultRepositoryInterface $diagnosticResultRepository
    ) {
        parent::__construct($context);
        $this->diagnosticResultFactory = $diagnosticResultFactory;
        $this->diagnosticResultRepository = $diagnosticResultRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = (array)$this->getRequest()->getParam('data');
        $isBefore = (bool)($data[DiagnosticResult::IS_BEFORE] ?? false);
        $version = (string)($data[DiagnosticResult::VERSION] ?? '');

        try {
            $diagnosticResult = $this->diagnosticResultRepository->getByVersionAndIsBefore($version, $isBefore);
        } catch (NoSuchEntityException $e) {
            $diagnosticResult = $this->diagnosticResultFactory->create();
        }

        $diagnosticResult->addData($data);

        $error = '';
        try {
            $this->diagnosticResultRepository->save($diagnosticResult);
        } catch (CouldNotSaveException $e) {
            $error = $e->getMessage();
        }

        if (!$error && $isBefore) {
            try {
                $this->diagnosticResultRepository->clearAfterResultByVersion($version);
            } catch (CouldNotSaveException $e) {
                $error = $e->getMessage();
            }
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultData = [
            'result' => !(bool)$error,
            'error' => $error
        ];
        $result->setData($resultData);

        return $result;
    }
}
