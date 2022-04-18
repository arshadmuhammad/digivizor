<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Api;

interface ImageQueueServiceInterface
{
    /**
     * @param \Amasty\ImageOptimizer\Api\Data\QueueInterface $queue
     *
     * @return void
     */
    public function addToQueue(\Amasty\ImageOptimizer\Api\Data\QueueInterface $queue): void;

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\QueueInterface $queue
     *
     * @return void
     */
    public function removeFromQueue(\Amasty\ImageOptimizer\Api\Data\QueueInterface $queue): void;

    /**
     * @param string $filename
     *
     * @return void
     */
    public function deleteByFilename(string $filename): void;

    /**
     * @param int $limit
     * @param array $queueTypes
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface[]
     */
    public function shuffleQueues(int $limit = 10, array $queueTypes = []): array;

    /**
     * @param array $queueTypes
     * @return void
     */
    public function clearQueue(array $queueTypes): void;

    /**
     * @return bool
     */
    public function isQueueEmpty(): bool;

    /**
     * @return int
     */
    public function getQueueSize(): int;
}
