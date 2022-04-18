<?php

namespace Amasty\Amp\Plugin\Shopby\Plugin\Ajax;

use Magento\Framework\View\Element\Template;

class ProductListWrapperPlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\Amp\Model\ConfigProvider $configProvider
    ) {
        $this->request = $request;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Amasty\Shopby\Plugin\Ajax\ProductListWrapper $subject
     * @param \Closure $proceed
     * @param Template $template
     * @param string $result
     * @return string
     */
    public function aroundAfterToHtml($subject, \Closure $proceed, Template $template, $result)
    {
        if (!$this->configProvider->isAmpEnabledOnCurrentPage($this->request->getActionName())) {
            $result = $proceed($template, $result);
        }

        return $result;
    }
}
