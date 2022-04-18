<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class RestoreFromOriginal implements ImageProcessorInterface
{
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
        if ($this->mediaDirectory->isExist(DumpOriginal::DUMP_DIRECTORY . $queue->getFilename())) {
            $this->mediaDirectory->copyFile(
                DumpOriginal::DUMP_DIRECTORY . $queue->getFilename(),
                $queue->getFilename()
            );
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        return false;
    }
}
