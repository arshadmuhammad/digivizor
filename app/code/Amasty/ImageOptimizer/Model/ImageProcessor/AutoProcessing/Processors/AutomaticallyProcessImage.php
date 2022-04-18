<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\Processors;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterfaceFactory;
use Amasty\ImageOptimizer\Api\Data\QueueInterfaceFactory;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\ImageSettingGetter;
use Amasty\ImageOptimizer\Model\ImageProcessor;

class AutomaticallyProcessImage implements AutoProcessorInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var ImageSettingGetter
     */
    private $imageSettingGetter;

    public function __construct(
        ImageProcessor $imageProcessor,
        ConfigProvider $configProvider,
        ImageSettingGetter $imageSettingGetter
    ) {
        $this->configProvider = $configProvider;
        $this->imageProcessor = $imageProcessor;
        $this->imageSettingGetter = $imageSettingGetter;
    }

    public function execute(string $imgPath): void
    {
        if (!$this->configProvider->isAutomaticallyOptimizeImages()) {
            return;
        }

        $imageSetting = $this->imageSettingGetter->get();
        if ($queueImage = $this->imageProcessor->prepareQueue($imgPath, $imageSetting)) {
            $this->imageProcessor->process($queueImage);
        }
    }
}
