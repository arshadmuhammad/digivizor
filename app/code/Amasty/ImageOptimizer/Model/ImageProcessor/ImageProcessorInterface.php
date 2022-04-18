<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;

interface ImageProcessorInterface
{
    public function process(QueueInterface $queue): void;

    /**
     * @param string                $file
     * @param ImageSettingInterface $imageSetting
     * @param QueueInterface        $queue
     *
     * @return bool
     * Return false if queue item can be skipped
     * @throws \Amasty\ImageOptimizer\Exceptions\ForceSkipAddToQueue
     */
    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool;
}
