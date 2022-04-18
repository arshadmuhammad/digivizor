<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class DumpOriginal implements ImageProcessorInterface
{
    const DUMP_DIRECTORY = 'amasty' . DIRECTORY_SEPARATOR . 'amoptimizer_dump' . DIRECTORY_SEPARATOR;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    public function __construct(Filesystem $filesystem)
    {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->isDumpOriginal()) {
            return;
        }

        $dumpImagePath = self::DUMP_DIRECTORY . $queue->getFilename();
        if (!$this->mediaDirectory->isExist($dumpImagePath)) {
            $this->mediaDirectory->copyFile($queue->getFilename(), $dumpImagePath);
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        $queue->setIsDumpOriginal($imageSetting->isDumpOriginal());

        return $queue->isDumpOriginal();
    }
}
