<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;

class ClearFolders
{
    const FOLDER_TYPE_WEBP = 'webp';
    const FOLDER_TYPE_MOBILE = 'mobile';
    const FOLDER_TYPE_TABLET = 'tablet';

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $folderType
     * @throws LocalizedException
     */
    public function execute(string $folderType): void
    {
        switch ($folderType) {
            case self::FOLDER_TYPE_WEBP:
                $this->clear(Resolutions::WEBP_DIR);
                break;
            case self::FOLDER_TYPE_MOBILE:
                $this->clear(Resolutions::RESOLUTIONS[Resolutions::MOBILE]['dir']);
                break;
            case self::FOLDER_TYPE_TABLET:
                $this->clear(Resolutions::RESOLUTIONS[Resolutions::TABLET]['dir']);
                break;
        }
    }

    /**
     * @param string $folder
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function clear(string $folder): void
    {
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($mediaDirectory->isDirectory($folder)) {
            foreach ($mediaDirectory->read($folder) as $item) {
                try {
                    $mediaDirectory->delete($item);
                } catch (\Exception $e) {
                    throw new LocalizedException(__('Couldn\'t clear `%1` folder', $folder));
                }
            }
        }
    }
}
