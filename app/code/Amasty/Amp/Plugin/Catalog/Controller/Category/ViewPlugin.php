<?php

namespace Amasty\Amp\Plugin\Catalog\Controller\Category;

use Amasty\Amp\Model\UrlConfigProvider;
use Amasty\Amp\Plugin\AmpRedirect;
use Magento\Framework\Controller\ResultInterface;
use Amasty\Amp\Model\ConfigProvider;
use Magento\Framework\Exception\NoSuchEntityException;

class ViewPlugin extends AmpRedirect
{
    /**
     * @return bool
     */
    protected function isNeedRedirect(): bool
    {
        return parent::isNeedRedirect() && $this->configProvider->isCategoryEnabled() && $this->isValidCategory();
    }

    /**
     * @param \Magento\Catalog\Controller\Category\View $controller
     * @param \Magento\Framework\View\Result\Page $result
     * @return ResultInterface|void
     */
    public function afterExecute($controller, $result)
    {
        if ($this->configProvider->isAmpCategoryPage()) {
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            if (!$this->configProvider->isValidCategory()) {
                $redirectUrl = $request->getDistroBaseUrl()
                    . str_replace('/' . UrlConfigProvider::AMP . '/', '', $request->getOriginalPathInfo());
                $response->setRedirect($redirectUrl);
            } else {
                if ($controller->getRequest()->getParam('amp_page')) {
                    $response->setBody(ConfigProvider::EMPTY_JSON);
                    $this->addAmpHeaders($response, $request);
                } else {
                    return $result;
                }
            }
        } else {
            return $result;
        }
    }

    /**
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\App\RequestInterface $request
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function addAmpHeaders($response, $request)
    {
        $url = $this->urlBuilder->getUrl(
            '*/*/*',
            ['_use_rewrite' => true, '_query' => $this->getFilterParams($request)]
        );
        $url = $this->urlConfigProvider->convertToAmpUrl($url);
        $this->urlConfigProvider->addAmpHeaders($response);
        $this->urlConfigProvider->setRedirect($url, $response);
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @return array
     */
    private function getFilterParams($request)
    {
        $params = $request->getParams();
        foreach ($params as $key => $param) {
            if (in_array($key, ['amp_page', 'id', '__amp_source_origin', 'p'])) {
                unset($params[$key]);
            }
        }

        return $params;
    }

    /**
     * @return bool
     */
    protected function isValidCategory()
    {
        $categoryId = (int)$this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            return false;
        }

        try {
            $category = $this->getCategoryRepository()->get($categoryId, $this->getStoreManager()->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $this->configProvider->isValidCategory($category);
    }
}
