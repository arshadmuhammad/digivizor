<?php

namespace Amasty\Amp\Block\Page\Html\Header;

class Logo extends \Magento\Theme\Block\Html\Header\Logo
{
    const AMASTY_AMP_LOGO_HTML = 'amasty/amp/logo_html';
    const AMASTY_AMP_AMP_LOGO_IMAGE = 'amasty_amp/amp/logo/image';

    /**
     * @return int
     */
    public function getLogoWidth()
    {
        return $this->getData('config')->getLogoWidth() ?: 150;
    }

    /**
     * @return int
     */
    public function getLogoHeight()
    {
        return $this->getData('config')->getLogoHeight() ?: 60;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        $storeLogoPath = $this->_scopeConfig->getValue(
            self::AMASTY_AMP_AMP_LOGO_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = self::AMASTY_AMP_LOGO_HTML . '/' . $storeLogoPath;
        $logoUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath == null || !$this->_isFile($path)) {
            $logoUrl = $this->_getLogoUrl();
        }

        return $logoUrl;
    }

    /**
     * @return string
     */
    public function getHomeUrl()
    {
        $url = $this->getUrl();
        $url = $this->getData('urlProvider') ? $this->getData('urlProvider')->modifyHomeUrl($url) : $url;

        return $url;
    }
}
