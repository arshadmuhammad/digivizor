<?php

namespace Amasty\Amp\Block;

use Magento\Framework\View\Element\Template;
use Amasty\Amp\Model\UrlConfigProvider;

class AmpLink extends \Magento\Framework\View\Element\Template
{
    const AMP_CANONICAL_PRODUCT = 'am_amp_canonical_product';
    const AMP_CANONICAL_CATEGORY = 'am_amp_canonical_category';
    const AMP_CANONICAL_CMS = 'am_amp_canonical_cms';
    const AMP_CANONICAL_HOME = 'am_amp_canonical_home';

    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $config;

    public function __construct(
        \Amasty\Amp\Model\ConfigProvider $config,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getAmpLink()
    {
        $ampLink = '';

        if ($this->isLinkShowed()) {
            $originalPath = $this->getRequest()->getOriginalPathInfo() ?: '/';
            $ampLink = $this->getBaseUrl() . UrlConfigProvider::AMP . $originalPath;
        }

        return $ampLink;
    }

    /**
     * @return bool
     */
    private function isLinkShowed()
    {
        $layoutName = $this->getNameInLayout();

        switch ($layoutName) {
            case self::AMP_CANONICAL_PRODUCT:
                $isShow = $this->config->isProductEnabled();
                break;
            case self::AMP_CANONICAL_CATEGORY:
                $isShow = $this->config->isCategoryEnabled() && $this->config->isValidCategory();
                break;
            case self::AMP_CANONICAL_CMS:
                $isShow = $this->config->isCmsEnabled();
                break;
            case self::AMP_CANONICAL_HOME:
                $isShow = $this->config->isHomeEnabled();
                break;
            default:
                $isShow = false;
                break;
        }

        return $isShow;
    }
}
