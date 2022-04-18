<?php

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image\Directory;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Reader
{
    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var FileSelectorInterface[]
     */
    private $fileSelectors;

    public function __construct(
        Filesystem $filesystem,
        array $fileSelectors = []
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);

        foreach ($fileSelectors as $selector) {
            if (!($selector instanceof FileSelectorInterface)) {
                throw new \LogicException(
                    sprintf('File selector must implement %s', FileSelectorInterface::class)
                );
            }
        }

        $this->fileSelectors = $fileSelectors;
    }

    public function execute(string $imageDirectory): array
    {
        if ($this->mediaDirectory->isExist($imageDirectory)) {
            $files = $this->mediaDirectory->readRecursively($imageDirectory);
        } else {
            $files = [];
        }

        /** @var FileSelectorInterface $selector */
        foreach ($this->fileSelectors as $selector) {
            $files = $selector->selectFiles($files, $imageDirectory);
        }

        return $files;
    }
}
