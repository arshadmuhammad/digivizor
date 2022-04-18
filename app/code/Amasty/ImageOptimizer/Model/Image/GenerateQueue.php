<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\ImageQueueServiceInterface;
use Amasty\ImageOptimizer\Model\Image\Directory\Reader;
use Amasty\ImageOptimizer\Model\ImageProcessor;
use Amasty\ImageOptimizer\Model\Queue\Queue;
use Magento\Framework\Filesystem\Io\File;

class GenerateQueue
{
    /**
     * @var \Amasty\ImageOptimizer\Model\Queue\ImageQueueService
     */
    private $imageQueueService;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var Reader
     */
    private $mediaReader;

    public function __construct(
        ImageQueueServiceInterface $imageQueueService,
        File $file,
        ImageProcessor $imageProcessor,
        Reader $mediaReader
    ) {
        $this->imageQueueService = $imageQueueService;
        $this->file = $file;
        $this->imageProcessor = $imageProcessor;
        $this->mediaReader = $mediaReader;
    }

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface[] $imageSettings
     * @param array $queueTypes
     *
     * @return int
     */
    public function generateQueue(array $imageSettings, array $queueTypes = [Queue::MANUAL]): int
    {
        $this->imageQueueService->clearQueue($queueTypes);
        $this->processFiles($imageSettings);

        return $this->imageQueueService->getQueueSize();
    }

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface[] $imageSettings
     *
     * @return void
     */
    public function processFiles(array $imageSettings): void
    {
        $folders = [];
        /** @var \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface $item */
        foreach ($imageSettings as $item) {
            foreach ($item->getFolders() as $folder) {
                $folders[$folder] = $item;
            }
        }

        foreach ($folders as $imageDirectory => $imageSetting) {
            $files = $this->mediaReader->execute($imageDirectory);

            foreach ($files as $file) {
                $pathInfo = $this->file->getPathInfo($file);
                if ($pathInfo['dirname'] !== $imageDirectory && isset($imageFolders[$pathInfo['dirname']])) {
                    continue;
                }
                if ($queue = $this->imageProcessor->prepareQueue($file, $imageSetting)) {
                    $queue->setQueueType(Queue::MANUAL);
                    $this->imageQueueService->addToQueue($queue);
                }
            }
        }
    }
}
