<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Magento\Framework\DataObject;

class ImageSetting extends DataObject implements ImageSettingInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const IMAGE_SETTING_ID = 'image_setting_id';
    const IS_ENABLED = 'is_enabled';
    const FOLDERS = 'folders';
    const TITLE = 'title';
    const IS_CREATE_MOBILE_RESOLUTION = 'is_create_mobile_resolution';
    const IS_CREATE_TABLET_RESOLUTION = 'is_create_tablet_resolution';
    const RESIZE_ALGORITHM = 'resize_algorithm';
    const IS_DUMP_ORIGINAL = 'is_create_dump';
    const JPEG_TOOL = 'jpeg_tool';
    const PNG_TOOL = 'png_tool';
    const GIF_TOOL = 'gif_tool';
    const WEBP_TOOL = 'webp_tool';
    /**#@-*/

    public function setImageSettingId(int $imageSettingId): ImageSettingInterface
    {
        return $this->setData(self::IMAGE_SETTING_ID, $imageSettingId);
    }

    public function getImageSettingId(): ?int
    {
        return $this->hasData(self::IMAGE_SETTING_ID) ? (int)$this->_getData(self::IMAGE_SETTING_ID): null;
    }

    public function setIsEnabled(bool $isEnabled): ImageSettingInterface
    {
        return $this->setData(self::IS_ENABLED, $isEnabled);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->_getData(self::IMAGE_SETTING_ID);
    }

    public function setFolders(array $folders): ImageSettingInterface
    {
        return $this->setData(self::FOLDERS, json_encode($folders));
    }

    public function getFolders(): array
    {
        $folders = $this->_getData(self::FOLDERS);
        if (empty($folders)) {
            return [];
        }
        if (!is_array($folders)) {
            $folders = json_decode($folders, true);
            if (json_last_error()) {
                return [];
            }
        }

        return $folders;
    }

    public function setTitle(string $title): ImageSettingInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getTitle(): ?string
    {
        return $this->hasData(self::TITLE) ? (string)$this->_getData(self::TITLE): null;
    }

    public function setIsCreateMobileResolution(bool $isCreateMobileResolution): ImageSettingInterface
    {
        return $this->setData(self::IS_CREATE_MOBILE_RESOLUTION, $isCreateMobileResolution);
    }

    public function isCreateMobileResolution(): bool
    {
        return (bool)$this->_getData(self::IS_CREATE_MOBILE_RESOLUTION);
    }

    public function setIsCreateTabletResolution(bool $isCreateTabletResolution): ImageSettingInterface
    {
        return $this->setData(self::IS_CREATE_TABLET_RESOLUTION, $isCreateTabletResolution);
    }

    public function isCreateTabletResolution(): bool
    {
        return (bool)$this->_getData(self::IS_CREATE_TABLET_RESOLUTION);
    }

    public function setResizeAlgorithm(int $resizeAlgorithm): ImageSettingInterface
    {
        return $this->setData(self::RESIZE_ALGORITHM, $resizeAlgorithm);
    }

    public function getResizeAlgorithm(): ?int
    {
        return $this->hasData(self::RESIZE_ALGORITHM) ? (int)$this->_getData(self::RESIZE_ALGORITHM): null;
    }

    public function setIsDumpOriginal(bool $isDumpOriginal): ImageSettingInterface
    {
        return $this->setData(self::IS_DUMP_ORIGINAL, $isDumpOriginal);
    }

    public function isDumpOriginal(): bool
    {
        return (bool)$this->_getData(self::IS_DUMP_ORIGINAL);
    }

    public function setJpegTool(string $jpegTool): ImageSettingInterface
    {
        return $this->setData(self::JPEG_TOOL, $jpegTool);
    }

    public function getJpegTool(): ?string
    {
        return $this->hasData(self::JPEG_TOOL) ? (string)$this->_getData(self::JPEG_TOOL) : null;
    }

    public function setPngTool(string $pngTool): ImageSettingInterface
    {
        return $this->setData(self::PNG_TOOL, $pngTool);
    }

    public function getPngTool(): ?string
    {
        return $this->hasData(self::PNG_TOOL) ? (string)$this->_getData(self::PNG_TOOL) : null;
    }

    public function setGifTool(string $gifTool): ImageSettingInterface
    {
        return $this->setData(self::GIF_TOOL, $gifTool);
    }

    public function getGifTool(): ?string
    {
        return $this->hasData(self::GIF_TOOL) ? (string)$this->_getData(self::GIF_TOOL) : null;
    }

    public function setWebpTool(string $webpTool): ImageSettingInterface
    {
        return $this->setData(self::WEBP_TOOL, $webpTool);
    }

    public function getWebpTool(): ?string
    {
        return $this->hasData(self::WEBP_TOOL) ? (string)$this->_getData(self::WEBP_TOOL) : null;
    }
}
