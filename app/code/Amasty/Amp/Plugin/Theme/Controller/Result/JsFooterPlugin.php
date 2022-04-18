<?php

declare(strict_types=1);

namespace Amasty\Amp\Plugin\Theme\Controller\Result;

use Amasty\Amp\Model\ConfigProvider;
use Magento\Theme\Controller\Result\JsFooterPlugin as MagentoJsFooterPlugin;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;

class JsFooterPlugin
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * Prevent move js to page bottom in case of AMP page layout specifications
     *
     * @param MagentoJsFooterPlugin $subject
     * @param \Closure $proceed
     * @param Http $response
     * @return mixed|void
     */
    public function aroundBeforeSendResponse(
        MagentoJsFooterPlugin $subject,
        \Closure $proceed,
        Http $response
    ) {
        if ($this->configProvider->isAmpUrl()) {
            return;
        }

        return $proceed($response);
    }

    public function aroundAfterRenderResult(
        MagentoJsFooterPlugin $subject,
        \Closure $proceed,
        Layout $subjectLayout,
        Layout $result,
        ResponseInterface $httpResponse
    ): Layout {
        if ($this->configProvider->isAmpUrl()) {
            return $result;
        }

        return $proceed($subjectLayout, $result, $httpResponse);
    }
}
