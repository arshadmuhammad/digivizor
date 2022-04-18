<?php

namespace Amasty\Amp\Plugin\Catalog\Block;

class TopmenuPlugin extends \Magento\Catalog\Plugin\Block\Topmenu
{
    /**
     * Build category tree for menu block.
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function beforeGetMenuHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        parent::beforeGetHtml($subject, $outermostClass, $childrenWrapClass, $limit);
    }
}
