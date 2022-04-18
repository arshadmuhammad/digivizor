<?php

namespace Amasty\Amp\Block\Category\Navigation;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
{
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $dataHelper;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function shouldDisplayProductCountOnLayer()
    {
        return $this->dataHelper->shouldDisplayProductCountOnLayer();
    }

    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function render(FilterInterface $filter)
    {
        if ($filter instanceof \Amasty\Shopby\Model\Layer\Filter\Category) {
            $items = $filter->getAmpItems() ?: [];
        } else {
            $items = $filter->getItems();
        }
  
        $this->assign('filterItems', $items);
        $html = $this->_toHtml();
        $this->assign('filterItems', []);

        return $html;
    }
}
