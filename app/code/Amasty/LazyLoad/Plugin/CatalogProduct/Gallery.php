<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Plugin\CatalogProduct;

use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyLoadProcessor;
use Magento\Catalog\Block\Product\View\Gallery as ImageGallery;
use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\DeviceDetect;

class Gallery
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
     * @var string
     */
    private $deviceType;

    /**
     * @var bool
     */
    private $isWebpSupport;

    public function __construct(
        ConfigProvider $configProvider,
        DeviceDetect $deviceDetect,
        OutputImage $outputImage,
        LazyLoadProcessor $lazyLoadProcessor
    ) {
        $this->configProvider = $configProvider;
        $this->outputImage = $outputImage;
        $this->deviceType = $deviceDetect->getDeviceType();
        $this->isWebpSupport = $deviceDetect->isUseWebP()
            && $lazyLoadProcessor->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT);
    }

    /**
     * @param ImageGallery $subject
     * @param $result
     *
     * @return mixed
     * @throws \Zend_Json_Exception
     */
    public function afterGetGalleryImagesJson(ImageGallery $subject, $result): string
    {
        if ($this->configProvider->isEnabled() && $this->isWebpSupport) {
            $imagesSettings = \Zend_Json::decode($result);

            foreach ($imagesSettings as &$imagesSetting) {
                foreach ($imagesSetting as &$image) {
                    if (is_string($image) && preg_match('/\.(jpg|jpeg|png|gif)$/', $image)) {
                        $image = $this->replaceWithBest($image);
                    }
                }
            }

            $result = \Zend_Json::encode($imagesSettings);
        }

        return $result;
    }

    private function replaceWithBest(string $imagePath): string
    {
        $outputImage = $this->outputImage->initialize($imagePath);

        if ($outputImage->process()) {
            return $outputImage->getBest($this->deviceType, $this->isWebpSupport);
        }

        return $imagePath;
    }
}
