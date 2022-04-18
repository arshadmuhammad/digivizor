<?php

namespace Amasty\Amp\Block\Page;

use Magento\Framework\View\Element\Template;
use Amasty\Amp\Model\ConfigProvider;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Theme\Block\Html\Title
     */
    private $title;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        Template\Context $context,
        \Magento\Theme\Block\Html\Title $title,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->title = $title;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCanonicalUrl()
    {
        switch ($this->_request->getFullActionName()) {
            case ConfigProvider::CMS_INDEX_INDEX:
                $url = $this->getBaseUrl();
                break;
            default:
                $url = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        switch ($this->_request->getFullActionName()) {
            case ConfigProvider::CATALOG_CATEGORY_VIEW:
                $category = $this->registry->registry('current_category');
                $title =  $category && $category->getMetaTitle() ? $category->getMetaTitle() : $category->getName();
                break;
            case ConfigProvider::CATALOG_PRODUCT_VIEW:
            case ConfigProvider::CMS_PAGE_VIEW:
            case ConfigProvider::CMS_INDEX_INDEX:
                $title = $this->title->getPageHeading();
                break;
            default:
                $title = '';
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->pageConfig->getKeywords();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->pageConfig->getDescription();
    }
}
