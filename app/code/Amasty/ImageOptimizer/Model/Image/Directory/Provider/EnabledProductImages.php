<?php

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image\Directory\Provider;

use Amasty\ImageOptimizer\Model\Collection\BatchLoader;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\Directory\FileSelectorInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;
use Magento\Catalog\Model\ResourceModel\Product;

class EnabledProductImages implements FileSelectorInterface
{
    /**
     * @var BatchLoader
     */
    private $batchLoader;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ReadHandler
     */
    private $galleryReadHandler;

    /**
     * @var Product\CollectionFactory
     */
    private $productCollectionFactory;

    private $disabledProductImageFiles = null;

    public function __construct(
        BatchLoader $batchLoader,
        ConfigProvider $configProvider,
        ReadHandler $galleryReadHandler,
        Product\CollectionFactory $productCollectionFactory
    ) {
        $this->batchLoader = $batchLoader;
        $this->configProvider = $configProvider;
        $this->galleryReadHandler = $galleryReadHandler;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function selectFiles(array $files, string $imageDirectory): array
    {
        if (!$this->isAllowed($imageDirectory)) {
            return $files;
        }

        return array_diff($files, $this->getDisabledProductImages());
    }

    private function getDisabledProductImages(): array
    {
        if ($this->disabledProductImageFiles === null) {
            /** @var Product\Collection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToFilter('status', Status::STATUS_DISABLED);

            foreach ($this->batchLoader->batchLoad($productCollection, 500) as $product) {
                $this->galleryReadHandler->execute($product);

                foreach ($product->getMediaGalleryImages() as $image) {
                    $this->disabledProductImageFiles[] = $product->getMediaConfig()
                        ->getMediaPath($image->getData('file'));
                }
            }

            $this->disabledProductImageFiles = array_unique($this->disabledProductImageFiles ?? []);
        }

        return $this->disabledProductImageFiles;
    }

    private function isAllowed(string $imageDirectory): bool
    {
        return $this->configProvider->isOptimizeEnabledProductImages()
            && strpos($imageDirectory, 'catalog') === 0;
    }
}
