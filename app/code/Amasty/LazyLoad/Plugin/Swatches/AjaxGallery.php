<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Plugin\Swatches;

use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

class AjaxGallery
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    public function __construct(
        ConfigProvider $configProvider,
        OutputImage $outputImage,
        DeviceDetect $deviceDetect
    ) {
        $this->configProvider = $configProvider;
        $this->outputImage = $outputImage;
        $this->deviceDetect = $deviceDetect;
    }

    public function afterGetJsonConfig(Configurable $subject, $result)
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isReplaceImagesUsingUserAgent()
            || !$this->deviceDetect->isUseWebP() || empty($result)
        ) {
            return $result;
        }

        $result = \Zend_Json::decode($result);

        if (!empty($result['images'])) {
            foreach ($result['images'] as &$gallery) {
                foreach ($gallery as &$imagesSetting) {
                    foreach ($imagesSetting as &$image) {
                        if (preg_match('/\.(jpg|jpeg|png|gif)$/', (string)$image)) {
                            $this->replaceWithWebp($image);
                        }
                    }
                }
            }
        }

        return \Zend_Json::encode($result);
    }

    /**
     * @param $imagePath
     */
    private function replaceWithWebp(&$imagePath)
    {
        $imagePath = $this->outputImage->initialize($imagePath)->getWebpPath() ? : $imagePath;
    }
}
