<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Model\ImageProcessor\DumpOriginal;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class ClearGeneratedImageForFile
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function execute($filePath): void
    {
        $filePath = $this->getMediaDirectory()->getRelativePath($filePath);
        $absolutePath = $this->getMediaDirectory()->getAbsolutePath($filePath);
        $dumpImagePath = DumpOriginal::DUMP_DIRECTORY . $filePath;

        if ($this->getMediaDirectory()->isExist($dumpImagePath)) {
            $this->getMediaDirectory()->delete($dumpImagePath);
        }
        foreach (Resolutions::RESOLUTIONS as $resolutionKey => $resolutionData) {
            $resolutionName = str_replace(
                $filePath,
                $resolutionData['dir'] . $filePath,
                $absolutePath
            );
            if ($this->getMediaDirectory()->isExist($resolutionName)) {
                $this->getMediaDirectory()->delete($resolutionName);
            }
        }
    }

    protected function getMediaDirectory(): WriteInterface
    {
        if (null === $this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}
