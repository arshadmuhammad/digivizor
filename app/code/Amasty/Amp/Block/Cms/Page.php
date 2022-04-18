<?php

namespace Amasty\Amp\Block\Cms;

class Page extends \Magento\Cms\Block\Page
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $actionName = $this->getRequest()->getFullActionName();
        if ($this->getData('configProvider')->isAmpEnabledOnCurrentPage($actionName)) {
            $ampContent = $this->getPage()->getAmpContent();
            $content = preg_replace('/\s+/', ' ', trim($ampContent))
                ? $ampContent
                : $this->getPage()->getContent();
            $html = $this->getData('validator')
                ->getValidHtml($this->_filterProvider->getPageFilter()->filter($content));
        } else {
            $html = parent::_toHtml();
        }

        return $html;
    }
}
