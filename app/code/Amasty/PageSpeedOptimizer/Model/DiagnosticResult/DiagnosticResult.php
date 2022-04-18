<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\DiagnosticResult;

use Amasty\PageSpeedOptimizer\Api\Data\DiagnosticResultInterface;
use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\ResourceModel\DiagnosticResult as DiagnosticResultResourceModel;
use Magento\Framework\Model\AbstractModel;

class DiagnosticResult extends AbstractModel implements DiagnosticResultInterface
{
    const RESULT_ID = 'result_id';
    const RESULT = 'result';
    const IS_BEFORE = 'is_before';
    const VERSION = 'version';

    public function _construct(): void
    {
        parent::_construct();
        $this->_init(DiagnosticResultResourceModel::class);
        $this->setIdFieldName(self::RESULT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getResultId(): ?int
    {
        return $this->hasData(self::RESULT_ID) ? (int)$this->_getData(self::RESULT_ID) : null;
    }

    /**
     * @inheritdoc
     */
    public function setResultId(?int $resultId): DiagnosticResultInterface
    {
        return $this->setData(self::RESULT_ID, $resultId);
    }

    /**
     * @inheritdoc
     */
    public function getResult(): ?string
    {
        $result = $this->_getData(self::RESULT);
        return $result === null ? null : (string)$result;
    }

    /**
     * @inheritdoc
     */
    public function setResult(?string $result): DiagnosticResultInterface
    {
        return $this->setData(self::RESULT, $result);
    }

    /**
     * @inheritdoc
     */
    public function getIsBefore(): bool
    {
        return (bool)$this->_getData(self::IS_BEFORE);
    }

    /**
     * @inheritdoc
     */
    public function setIsBefore(bool $isBefore): DiagnosticResultInterface
    {
        return $this->setData(self::IS_BEFORE, $isBefore);
    }

    /**
     * @inheritdoc
     */
    public function getVersion(): string
    {
        return (string)$this->_getData(self::VERSION);
    }

    /**
     * @inheritdoc
     */
    public function setVersion(string $version): DiagnosticResultInterface
    {
        return $this->setData(self::VERSION, $version);
    }
}
