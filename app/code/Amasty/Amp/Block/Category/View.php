<?php

namespace Amasty\Amp\Block\Category;

class View extends \Magento\Catalog\Block\Category\View
{
    const RECOMMEND_WIDTH = 180;
    const RECOMMEND_HEIGHT = 100;

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return $this->getData('configProvider')->getCategoryImageWidth() ?: self::RECOMMEND_WIDTH;
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return $this->getData('configProvider')->getCategoryImageHeight() ?: self::RECOMMEND_HEIGHT;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $currentCategory = $this->getCurrentCategory();

        return $this->getData('outputHelper')->categoryAttribute(
            $this->getCurrentCategory(),
            $currentCategory->getDescription(),
            'description'
        );
    }

    /**
     * @return string
     */
    public function getCmsContent()
    {
        $cmsContent= $this->getCmsBlockHtml();
        $resultCmsContent = $this->getData('htmlValidator')->getValidHtml($cmsContent);

        return $cmsContent == $resultCmsContent ? $cmsContent : '';
    }
}
