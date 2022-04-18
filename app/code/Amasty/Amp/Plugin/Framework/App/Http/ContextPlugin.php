<?php

declare(strict_types=1);

namespace Amasty\Amp\Plugin\Framework\App\Http;

use Amasty\Amp\Model\ConfigProvider;
use Amasty\Amp\Model\Detection\MobileDetect;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Checkout\Helper\Cart;

class ContextPlugin
{
    const AMP_HTTP_CONTEXT_KEY = 'amasty_amp_mobile_device';
    const AMP_CART_HTTP_CONTEXT_KEY = 'amasty_amp_cart';
    const AMP_HTTP_CONTEXT_VALUE = 'amp';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    /**
     * @var Cart
     */
    private $cart;

    public function __construct(
        ConfigProvider $configProvider,
        MobileDetect $mobileDetect,
        Cart $cart
    ) {
        $this->configProvider = $configProvider;
        $this->mobileDetect = $mobileDetect;
        $this->cart = $cart;
    }

    /**
     * @param HttpContext $subject
     */
    public function beforeGetVaryString(HttpContext $subject)
    {
        $subject->setValue(self::AMP_CART_HTTP_CONTEXT_KEY, (bool)$this->cart->getSummaryCount(), '');
        if ($this->configProvider->isRedirectMobile() && $this->mobileDetect->isMobile()) {
            $subject->setValue(self::AMP_HTTP_CONTEXT_KEY, self::AMP_HTTP_CONTEXT_VALUE, '');
        }
    }
}
