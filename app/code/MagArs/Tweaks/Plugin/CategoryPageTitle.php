<?php


namespace MagArs\Tweaks\Plugin;


class CategoryPageTitle {

    protected $_request;

    protected $registry;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->_request = $request;
        $this->registry = $registry;
    }

    public function aroundSetPageTitle(\Magento\Theme\Block\Html\Title $subject, \Closure $proceed, $pageTitle) {

        if ($this->_request->getFullActionName() == 'catalog_category_view') {
            $category = $this->registry->registry('current_category');
            if($category->getCategoryName2()){
                return $proceed(__($category->getCategoryName2()));
            }
        }

        return $proceed($pageTitle);
    }

}
