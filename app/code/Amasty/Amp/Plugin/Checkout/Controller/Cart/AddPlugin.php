<?php

namespace Amasty\Amp\Plugin\Checkout\Controller\Cart;

use Amasty\Amp\Model\ConfigProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\Session;
use Magento\Framework\Message\Manager;

class AddPlugin
{
    const AMP_OPTIONS_FLAG = 'amamp_option_flag';
    const INVALID_OPT = 'forse_disabled';

    /**
     * @var \Amasty\Amp\Model\UrlConfigProvider
     */
    private $urlConfigProvider;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        \Amasty\Amp\Model\UrlConfigProvider $urlConfigProvider,
        \Amasty\Amp\Model\ConfigProvider $configProvider,
        Session $session,
        \Magento\Framework\Serialize\Serializer\Json $json,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request,
        ManagerInterface $messageManager
    ) {
        $this->urlConfigProvider = $urlConfigProvider;
        $this->session = $session;
        $this->json = $json;
        $this->configProvider = $configProvider;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Action $controller
     * @param $result
     * @return $result|void
     */
    public function afterExecute(Action $controller, $result)
    {
        if ($this->configProvider->isAmpCategoryPage() || $this->configProvider->isAmpProductPage()) {
            $response = $controller->getResponse();
            $this->urlConfigProvider->addAmpHeaders($response);
            $message = $this->prepareMessage();
            if ($message) {
                $response->setBody($message);
            } else {
                $productId = (int)$controller->getRequest()->getParam('product');
                $product = $this->productRepository->getById($productId);
                $invalidParam = $this->configProvider->isRedirectMobile()
                    ? sprintf('?%s=1', self::INVALID_OPT)
                    : '';
                $controller->getResponse()->setBody(ConfigProvider::EMPTY_JSON);
                $this->urlConfigProvider->setRedirect($product->getProductUrl() . $invalidParam, $response);
            }
        } else {
            return $result;
        }
    }

    /**
     * @return bool|\Magento\Framework\Phrase|string
     */
    private function prepareMessage()
    {
        $messages = $this->session->getData(Manager::DEFAULT_GROUP);
        $resultMessage = null;
        if (!$messages) {
            return null;
        }

        $message = $messages->getLastAddedMessage();
        $type = $this->configProvider->getMessageType($message);
        $cartUrl = $text = '';
        if ($message) {
            if ($type !== 'error' && $message->getIdentifier() == 'addCartSuccessMessage') {
                $arguments = $message->getData();
                $resultMessage = __(
                    'You added %1 to your',
                    $arguments['product_name'],
                    $arguments['cart_url']
                );
                $cartUrl = $arguments['cart_url'];
                $text = $resultMessage->render();
            } else {
                if (!$this->request->getParam(self::AMP_OPTIONS_FLAG, false)) {
                    $text = $message->getText();
                }
            }

            if ($text) {
                $resultMessage = $this->json->serialize([
                    $type => htmlspecialchars_decode($text, ENT_QUOTES),
                    'cart_url' => $cartUrl
                ]);
            }
        }

        if ($resultMessage) {
            $this->session->setData(Manager::DEFAULT_GROUP, null);
        }

        return $resultMessage;
    }
}
