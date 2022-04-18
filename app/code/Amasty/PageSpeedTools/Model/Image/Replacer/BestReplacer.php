<?php

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Image\Replacer;

use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Amasty\PageSpeedTools\Model\Image\ReplacerInterface;

class BestReplacer implements ReplacerInterface
{
    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    public function __construct(
        OutputImage $outputImage,
        DeviceDetect $deviceDetect
    ) {
        $this->outputImage = $outputImage;
        $this->deviceDetect = $deviceDetect;
    }

    public function execute(string $image, string $imagePath): string
    {
        $outputImage = $this->outputImage->initialize($imagePath);

        if ($outputImage->process()) {
            return str_replace(
                $imagePath,
                $outputImage->getBest(...$this->deviceDetect->getDeviceParams()),
                $image
            );
        }

        return $image;
    }
}
