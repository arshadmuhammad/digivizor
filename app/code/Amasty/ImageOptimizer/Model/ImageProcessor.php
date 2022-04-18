<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterfaceFactory;
use Amasty\ImageOptimizer\Exceptions\ForceSkipAddToQueue;
use Amasty\ImageOptimizer\Model\ImageProcessor\Strategy;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Io\File;

class ImageProcessor
{
    /**
     * @var array
     */
    private $strategies;

    /**
     * @var File
     */
    private $file;

    /**
     * @var QueueInterfaceFactory
     */
    private $queueFactory;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        QueueInterfaceFactory $queueFactory,
        array $strategies,
        File $file,
        Filesystem $filesystem
    ) {
        $this->strategies = $strategies;
        $this->file = $file;
        $this->queueFactory = $queueFactory;
        $this->filesystem = $filesystem;
    }

    public function process(QueueInterface $queue): void
    {
        $extension = strtolower($queue->getExtension());
        /** @var Strategy $strategy */
        foreach ($this->strategies as $strategy) {
            if (in_array($extension, $strategy->getExtensions())) {
                foreach ($strategy->getStrategy() as $processor) {
                    $processor->process($queue);
                }
            }
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting): ?QueueInterface
    {
        $pathInfo = $this->file->getPathInfo($file);
        if (!isset($pathInfo['extension'])) {
            return null;
        }
        $queue = $this->queueFactory->create();
        $queue->setFilename($this->getMediaDirectory()->getRelativePath($file));
        $queue->setExtension($pathInfo['extension']);
        $processQueue = false;
        foreach ($this->strategies as $strategy) {
            if (in_array($queue->getExtension(), $strategy->getExtensions())) {
                /** @var ImageProcessor\ImageProcessorInterface $processor */
                foreach ($strategy->getStrategy() as $processor) {
                    try {
                        if ($processor->prepareQueue($file, $imageSetting, $queue)) {
                            $processQueue = true;
                        }
                    } catch (ForceSkipAddToQueue $e) {
                        return null;
                    }
                }
            }
        }
        if ($processQueue) {
            return $queue;
        }

        return null;
    }

    public function getMediaDirectory(): ReadInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }

    public function setMediaDirectory(ReadInterface $mediaDirectory): void
    {
        $this->mediaDirectory = $mediaDirectory;
    }
}
