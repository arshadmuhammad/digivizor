<?php

namespace Amasty\Amp\Block\Product\Content\View;

class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{
    /**
     * @param $images
     * @param $helper
     * @return string
     */
    public function getMainImageData($images, $helper)
    {
        $mainImage = current(array_filter($images, function ($img) {
            return $this->isMainImage($img);
        }));

        if (!empty($images) && empty($mainImage)) {
            $mainImage = $this->getGalleryImages()->getFirstItem();
        }

        return $mainImage ? $mainImage->getData('medium_image_url') : $helper->getDefaultPlaceholderUrl('image');
    }
}
