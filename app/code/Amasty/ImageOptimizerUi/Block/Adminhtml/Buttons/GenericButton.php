<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons;

class GenericButton
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
    }

    public function getUrl($route = '', $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
