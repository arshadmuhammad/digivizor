<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

class ImagesExampleProvider
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    public function get(): array
    {
        return [
            'jpegoptim100' => $this->getViewFileUrl('Amasty_ImageOptimizerUi::images/jpg_example_100.jpg'),
            'jpegoptim90' => $this->getViewFileUrl('Amasty_ImageOptimizerUi::images/jpg_example_90.jpg'),
            'jpegoptim80' => $this->getViewFileUrl('Amasty_ImageOptimizerUi::images/jpg_example_80.jpg')
        ];
    }

    private function getViewFileUrl(string $path): string
    {
        return $this->assetRepo->getUrl($path);
    }
}
