<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Api\Data;

interface QueueInterface
{
    /**
     * @return int
     */
    public function getQueueId(): ?int;

    /**
     * @param int $queueId
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setQueueId(?int $queueId): QueueInterface;

    /**
     * @return string
     */
    public function getFilename(): ?string;

    /**
     * @param string $filename
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setFilename(string $filename): QueueInterface;

    /**
     * @return string
     */
    public function getExtension(): ?string;

    /**
     * @param string $extension
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setExtension(string $extension): QueueInterface;

    /**
     * @return array
     */
    public function getResolutions(): array;

    /**
     * @param array $resolutions
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setResolutions(array $resolutions): QueueInterface;

    /**
     * @return string
     */
    public function getTool(): ?string;

    /**
     * @param string $tool
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setTool(string $tool): QueueInterface;

    /**
     * @return string $webpTool
     */
    public function getWebpTool(): ?string;

    /**
     * @param string $webpTool
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setWebpTool(string $webpTool): QueueInterface;

    /**
     * @return int
     */
    public function getResizeAlgorithm(): ?int;

    /**
     * @param int $resizeAlgorithm
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setResizeAlgorithm(int $resizeAlgorithm): QueueInterface;

    /**
     * @return bool
     */
    public function isDumpOriginal(): ?bool;

    /**
     * @param bool $isDumpOriginal
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setIsDumpOriginal(bool $isDumpOriginal): QueueInterface;

    /**
     * @param string $queueType
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface
     */
    public function setQueueType(string $queueType): QueueInterface;

    /**
     * @return string
     */
    public function getQueueType(): string;
}
