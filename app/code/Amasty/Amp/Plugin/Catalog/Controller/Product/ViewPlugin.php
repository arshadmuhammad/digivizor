<?php

namespace Amasty\Amp\Plugin\Catalog\Controller\Product;

use Amasty\Amp\Model\ConfigProvider;
use Amasty\Amp\Plugin\AmpRedirect;
use Magento\Framework\Controller\ResultInterface;
use Amasty\Amp\Plugin\Checkout\Controller\Cart\AddPlugin;

class ViewPlugin extends AmpRedirect
{
    /**
     * @param \Magento\Catalog\Controller\Category\View $controller
     * @param \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Redirect $result
     * @return ResultInterface|void
     */
    public function afterExecute($controller, $result)
    {
        $request = $controller->getRequest();
        if ($this->configProvider->isAmpProductPage()
            && $request->getParam('amp_page')
            && $request->getParam('options') == 'cart'
        ) {
            $controller->getResponse()->setBody(ConfigProvider::EMPTY_JSON);
            $this->addAmpHeaders($controller->getResponse());
        } else {
            return $result;
        }
    }

    /**
     * @return bool
     */
    protected function isNeedRedirect(): bool
    {
        return parent::isNeedRedirect()
            && $this->configProvider->isProductEnabled()
            && !$this->getRequest()->getParam(AddPlugin::INVALID_OPT);
    }

    /**
     * @param \Magento\Framework\App\ResponseInterface $response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addAmpHeaders($response)
    {
        $url = $this->urlConfigProvider->convertToAmpUrl($this->redirect->getRedirectUrl());
        $this->urlConfigProvider->addAmpHeaders($response);
        $this->urlConfigProvider->setRedirect($url, $response);
    }
}
