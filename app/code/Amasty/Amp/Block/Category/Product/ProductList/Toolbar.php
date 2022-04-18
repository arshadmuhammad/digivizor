<?php

namespace Amasty\Amp\Block\Category\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * @var \Amasty\Amp\Block\Category\Product\ProductList\Toolbar\Pager|null
     */
    private $pager;

    /**
     * @param string $sorter
     * @return string
     */
    public function getSorterUrl($sorter)
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = ['product_list_order' => $sorter];
        $url = $this->getUrl('*/*/*', $urlParams);

        return $this->getData('urlConfig')->convertToAmpUrl($url);
    }

    /**
     * @return int
     */
    public function getLastPageNum()
    {
        return $this->getPagerBlock()->getLastPageNum();
    }

    /**
     * @return int
     */
    public function getFirstNum()
    {
        return $this->getPagerBlock()->getFirstNum();
    }

    /**
     * @return int
     */
    public function getLastNum()
    {
        return $this->getPagerBlock()->getLastNum();
    }

    /**
     * @return int
     */
    public function getTotalNum()
    {
        return $this->getPagerBlock()->getTotalNum();
    }

    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPagerBlock()
    {
        if (!$this->pager) {
            $this->pager = $this->getLayout()->getBlock('product_list_toolbar_pager');
        }

        return $this->pager;
    }
}
