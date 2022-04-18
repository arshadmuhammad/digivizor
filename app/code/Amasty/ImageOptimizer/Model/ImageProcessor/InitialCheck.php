<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Exceptions\ForceSkipAddToQueue;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class InitialCheck implements ImageProcessorInterface
{

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function process(QueueInterface $queue): void
    {
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        foreach (Resolutions::RESOLUTIONS as $resolution) {
            if (strpos($file, $resolution['dir']) !== false) {
                throw new ForceSkipAddToQueue();
            }
        }
        if (strpos($file, DumpOriginal::DUMP_DIRECTORY) !== false) {
            throw new ForceSkipAddToQueue();
        }

        if (!$this->mediaDirectory->isFile($file)) {
            throw new ForceSkipAddToQueue();
        }

        return false;
    }
}
