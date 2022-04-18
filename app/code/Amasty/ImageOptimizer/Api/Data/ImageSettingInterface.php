<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Api\Data;

interface ImageSettingInterface
{
    /**
     * @param int $imageSettingId
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setImageSettingId(int $imageSettingId): ImageSettingInterface;

    /**
     * @return int
     */
    public function getImageSettingId(): ?int;

    /**
     * @param bool $isEnabled
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setIsEnabled(bool $isEnabled): ImageSettingInterface;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param array $folders
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setFolders(array $folders): ImageSettingInterface;

    /**
     * @return array
     */
    public function getFolders(): array;

    /**
     * @param string $title
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setTitle(string $title): ImageSettingInterface;

    /**
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * @param bool $isCreateMobileResolution
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setIsCreateMobileResolution(bool $isCreateMobileResolution): ImageSettingInterface;

    /**
     * @return bool
     */
    public function isCreateMobileResolution(): bool;

    /**
     * @param bool $isCreateTabletResolution
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setIsCreateTabletResolution(bool $isCreateTabletResolution): ImageSettingInterface;

    /**
     * @return bool
     */
    public function isCreateTabletResolution(): bool;

    /**
     * @param int $resizeAlgorithm
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setResizeAlgorithm(int $resizeAlgorithm): ImageSettingInterface;

    /**
     * @return int
     */
    public function getResizeAlgorithm(): ?int;

    /**
     * @param bool $isDumpOriginal
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setIsDumpOriginal(bool $isDumpOriginal): ImageSettingInterface;

    /**
     * @return bool
     */
    public function isDumpOriginal(): bool;

    /**
     * @param string $jpegTool
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setJpegTool(string $jpegTool): ImageSettingInterface;

    /**
     * @return string
     */
    public function getJpegTool(): ?string;

    /**
     * @param string $pngTool
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setPngTool(string $pngTool): ImageSettingInterface;

    /**
     * @return string
     */
    public function getPngTool(): ?string;

    /**
     * @param string $gifTool
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setGifTool(string $gifTool): ImageSettingInterface;

    /**
     * @return string
     */
    public function getGifTool(): ?string;

    /**
     * @param string $webpTool
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     */
    public function setWebpTool(string $webpTool): ImageSettingInterface;

    /**
     * @return string
     */
    public function getWebpTool(): ?string;
}
