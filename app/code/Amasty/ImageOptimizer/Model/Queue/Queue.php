<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Queue;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Magento\Framework\Model\AbstractModel;

class Queue extends AbstractModel implements QueueInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const QUEUE_ID = 'queue_id';
    const FILENAME = 'filename';
    const EXTENSION = 'extension';
    const TOOL = 'tool';
    const WEBP_TOOL = 'webp_tool';
    const RESOLUTIONS = 'resolutions';
    const DUMP_ORIGINAL = 'dump_original';
    const RESIZE_ALGORITHM = 'resize_algorithm';
    const QUEUE_TYPE = 'queue_type';
    /**#@-*/

    const MANUAL = 'manual';
    const CRON = 'cron';

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\ImageOptimizer\Model\Queue\ResourceModel\Queue::class);
        $this->setIdFieldName(self::QUEUE_ID);
    }

    public function getQueueId(): ?int
    {
        return $this->hasData(self::QUEUE_ID) ? (int)$this->_getData(self::QUEUE_ID) : null;
    }

    public function setQueueId(?int $queueId): QueueInterface
    {
        return $this->setData(self::QUEUE_ID, $queueId);
    }

    public function getFilename(): ?string
    {
        return $this->hasData(self::FILENAME) ? (string)$this->_getData(self::FILENAME) : null;
    }

    public function setFilename(string $filename): QueueInterface
    {
        return $this->setData(self::FILENAME, $filename);
    }

    public function getExtension(): ?string
    {
        return $this->hasData(self::EXTENSION) ? (string)$this->_getData(self::EXTENSION) : null;
    }

    public function setExtension(string $extension): QueueInterface
    {
        return $this->setData(self::EXTENSION, $extension);
    }

    public function getResolutions(): array
    {
        $data = $this->_getData(self::RESOLUTIONS);
        if (empty($data)) {
            return [];
        }

        return explode(',', $data);
    }

    public function setResolutions(array $resolutions): QueueInterface
    {
        if (is_array($resolutions)) {
            $resolutions = implode(',', $resolutions);
        }

        return $this->setData(self::RESOLUTIONS, $resolutions);
    }

    public function getTool(): ?string
    {
        return $this->hasData(self::TOOL) ? (string)$this->_getData(self::TOOL) : null;
    }

    public function setTool(string $tool): QueueInterface
    {
        return $this->setData(self::TOOL, $tool);
    }

    public function getWebpTool(): ?string
    {
        return $this->hasData(self::WEBP_TOOL) ? (string)$this->_getData(self::WEBP_TOOL) : null;
    }

    public function setWebpTool(string $webpTool): QueueInterface
    {
        return $this->setData(self::WEBP_TOOL, $webpTool);
    }

    public function isDumpOriginal(): ?bool
    {
        return $this->hasData(self::DUMP_ORIGINAL) ? (bool)$this->_getData(self::DUMP_ORIGINAL) : null;
    }

    public function setIsDumpOriginal(bool $isDumpOriginal): QueueInterface
    {
        return $this->setData(self::DUMP_ORIGINAL, $isDumpOriginal);
    }

    public function getResizeAlgorithm(): ?int
    {
        return $this->hasData(self::RESIZE_ALGORITHM) ? (int)$this->_getData(self::RESIZE_ALGORITHM): null;
    }

    public function setResizeAlgorithm(int $resizeAlgorithm): QueueInterface
    {
        return $this->setData(self::RESIZE_ALGORITHM, (int)$resizeAlgorithm);
    }

    public function getQueueType(): string
    {
        return $this->_getData(self::QUEUE_TYPE);
    }

    public function setQueueType(string $queueType): QueueInterface
    {
        return $this->setData(self::QUEUE_TYPE, $queueType);
    }
}
