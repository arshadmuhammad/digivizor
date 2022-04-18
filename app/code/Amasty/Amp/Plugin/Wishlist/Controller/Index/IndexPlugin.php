<?php

namespace Amasty\Amp\Plugin\Wishlist\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

class IndexPlugin
{
    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Amasty\Amp\Model\ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param ActionInterface $subject
     * @param $request
     */
    public function aroundBeforeDispatch(
        $subject,
        callable $proceed,
        ActionInterface $subjectProcessed,
        RequestInterface $request
    ) {
        if (!$this->configProvider->isAmpProductPage()) {
            $proceed($subjectProcessed, $request);
        }
    }
}
