<?php

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;

class CheckTools
{
    /**
     * @var CommandProvider
     */
    private $jpegCommandProvider;

    /**
     * @var CommandProvider
     */
    private $pngCommandProvider;

    /**
     * @var CommandProvider
     */
    private $gifCommandProvider;

    /**
     * @var CommandProvider
     */
    private $webpCommandProvider;

    public function __construct(
        CommandProvider $jpegCommandProvider,
        CommandProvider $pngCommandProvider,
        CommandProvider $gifCommandProvider,
        CommandProvider $webpCommandProvider
    ) {
        $this->jpegCommandProvider = $jpegCommandProvider;
        $this->pngCommandProvider = $pngCommandProvider;
        $this->gifCommandProvider = $gifCommandProvider;
        $this->webpCommandProvider = $webpCommandProvider;
    }

    public function check(ImageSettingInterface $model): array
    {
        $errors = [];
        if ($model->getJpegTool()) {
            $tool = $this->jpegCommandProvider->get($model->getJpegTool());
            if (!$tool->isAvailable()) {
                $errors[] = $tool->getUnavailableReason();
            }
        }

        if ($model->getPngTool()) {
            $tool = $this->pngCommandProvider->get($model->getPngTool());
            if (!$tool->isAvailable()) {
                $errors[] = $tool->getUnavailableReason();
            }
        }

        if ($model->getGifTool()) {
            $tool = $this->gifCommandProvider->get($model->getGifTool());
            if (!$tool->isAvailable()) {
                $errors[] = $tool->getUnavailableReason();
            }
        }

        if ($model->getWebpTool()) {
            $tool = $this->webpCommandProvider->get($model->getWebpTool());
            if (!$tool->isAvailable()) {
                $errors[] = $tool->getUnavailableReason();
            }
        }

        return $errors;
    }
}
