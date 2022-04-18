<?php

namespace Amasty\Amp\Block\Page\Html\Header;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\Helper\Data as UrlHelper;

class Switcher extends \Magento\Store\Block\Switcher
{
    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    private $encoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Url\EncoderInterface $encoder,
        array $data = [],
        UrlHelper $urlHelper = null
    ) {
        parent::__construct($context, $postDataHelper, $data, $urlHelper);
        $this->encoder = $encoder;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     *
     * @return string
     */
    public function getStoreUrlAmp(\Magento\Store\Model\Store $store)
    {
        return $this->_urlBuilder->getUrl(
            'stores/store/redirect',
            [
                '___store' => $store->getCode(),
                '___from_store' => $this->_storeManager->getStore()->getCode(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->encoder->encode(
                    $store->getCurrentUrl(false)
                ),
            ]
        );
    }

    /**
     * @param $store
     * @return bool
     */
    public function isCurrentStore($store)
    {
        return $store->getId() == $this->getCurrentStoreId();
    }
}
