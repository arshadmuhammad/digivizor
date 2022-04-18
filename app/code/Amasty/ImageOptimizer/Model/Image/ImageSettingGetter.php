<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\ImageSettingInterfaceFactory;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;

class ImageSettingGetter
{
    /**
     * @var ImageSettingInterfaceFactory
     */
    private $imageSettingFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ImageSettingInterfaceFactory $imageSettingFactory,
        ConfigProvider $configProvider
    ) {
        $this->imageSettingFactory = $imageSettingFactory;
        $this->configProvider = $configProvider;
    }

    public function get(): ImageSettingInterface
    {
        $imageSetting = $this->imageSettingFactory->create();
        $imageSetting->setJpegTool($this->configProvider->getJpegCommand());
        $imageSetting->setPngTool($this->configProvider->getPngCommand());
        $imageSetting->setGifTool($this->configProvider->getGifCommand());
        $imageSetting->setWebpTool($this->configProvider->getWebpCommand());
        $imageSetting->setIsDumpOriginal($this->configProvider->isDumpOriginal());
        //TODO make resolution as array
        $resolutions = $this->configProvider->getResolutions();
        if (in_array(Resolutions::MOBILE, $resolutions)) {
            $imageSetting->setIsCreateMobileResolution(true);
        }
        if (in_array(Resolutions::TABLET, $resolutions)) {
            $imageSetting->setIsCreateTabletResolution(true);
        }
        $imageSetting->setResizeAlgorithm($this->configProvider->getResizeAlgorithm());

        return $imageSetting;
    }
}
