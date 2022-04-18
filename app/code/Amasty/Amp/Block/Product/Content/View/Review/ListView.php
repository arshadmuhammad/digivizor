<?php

namespace Amasty\Amp\Block\Product\Content\View\Review;

class ListView extends \Magento\Review\Block\Product\View\ListView
{
    const LIMIT = 4;
    const AMASTY_AMP_REVIEW_LIST_AJAX = 'amasty_amp/review/listAjax/';

    /**
     * @return \Magento\Review\Block\Product\View\ListView|void
     */
    protected function _prepareLayout()
    {
        if ($this->getRequest()->getParam('id')) {
            /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
            $pagerBlock = $this->getLayout()->getBlock('product_review_list.toolbar');
            if ($pagerBlock) {
                $pagerBlock->setAvailableLimit([self::LIMIT]);
                $pagerBlock->setLimit(self::LIMIT);
            }

            return parent::_prepareLayout();
        }
    }

    /**
     * @return string
     */
    public function getLoadUrl()
    {
        $baseUrl = $this->_storeManager->getStore()->getUrl();

        return str_replace(
            ['https:', 'http:'],
            '',
            $baseUrl . self::AMASTY_AMP_REVIEW_LIST_AJAX . 'productId/' . $this->getProductId() . '?p=1'
        );
    }
}
