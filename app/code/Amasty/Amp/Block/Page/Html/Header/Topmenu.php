<?php

namespace Amasty\Amp\Block\Page\Html\Header;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function getMenuHtml($outermostClass = '', $childrenWrapClass = '')
    {
        $this->getMenu()->setOutermostClass($outermostClass);
        $this->getMenu()->setChildrenWrapClass($childrenWrapClass);

        $html = $this->getHtmlContent($this->getMenu());

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     *
     * @return string
     */
    private function getHtmlContent(\Magento\Framework\Data\Tree\Node $menuTree)
    {
        $html = '';
        $children = $menuTree->getChildren();

        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            $topmenuItem = $this->getData('topmenuItem');
            $topmenuItem->setItem($child);

            $html .= $topmenuItem->toHtml();
        }

        return $html;
    }

    /**
     * Override parent method
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $keyInfo = \Magento\Framework\View\Element\Template::getCacheKeyInfo();
        $keyInfo[] = 'amp';

        return $keyInfo;
    }
}
