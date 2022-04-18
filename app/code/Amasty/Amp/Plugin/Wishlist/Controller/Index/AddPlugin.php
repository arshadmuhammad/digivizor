<?php

namespace Amasty\Amp\Plugin\Wishlist\Controller\Index;

use Amasty\Amp\Model\ConfigProvider;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\Session;

class AddPlugin
{
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
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\Url
     */
    private $urlBuilder;

    public function __construct(
        \Amasty\Amp\Model\UrlConfigProvider $urlConfigProvider,
        \Amasty\Amp\Model\ConfigProvider $configProvider,
        Session $session,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $urlBuilder
    ) {
        $this->urlConfigProvider = $urlConfigProvider;
        $this->session = $session;
        $this->json = $json;
        $this->configProvider = $configProvider;
        $this->customerSession = $customerSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param Action $controller
     * @param callable $proceed
     * @return \Magento\Framework\Controller\Result\Redirect|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundExecute(Action $controller, callable $proceed)
    {
        if ($this->configProvider->isAmpProductPage()) {
            $this->urlConfigProvider->addAmpHeaders($controller->getResponse());
            if (!$this->customerSession->isLoggedIn()) {
                $this->urlConfigProvider->setRedirect($this->urlBuilder->getLoginUrl(), $controller->getResponse());
            } else {
                $proceed();
            }

            $controller->getResponse()->setBody($this->prepareMessage());
        } else {
            return $proceed();
        }
    }

    /**
     * @return string
     */
    private function prepareMessage()
    {
        $messages = $this->session->getData(Manager::DEFAULT_GROUP);
        $this->session->setData(Manager::DEFAULT_GROUP, null);
        $resultMessage = '';
        if ($messages) {
            $message = $messages->getLastAddedMessage();
            $type = $this->configProvider->getMessageType($message);
            if ($message) {
                if ($type !== 'error' && $message->getIdentifier() == 'addProductSuccessMessage') {
                    $arguments = $message->getData();
                    $resultMessage = __(
                        '%1 has been added to your Wish List.',
                        $arguments['product_name']
                    );
                    $text = $resultMessage->render();
                } else {
                    $text = $message->getText();
                }

                $resultMessage = $this->json->serialize([
                    $type => htmlspecialchars_decode($text, ENT_QUOTES),
                ]);
            }
        }

        return $resultMessage ?: ConfigProvider::EMPTY_JSON;
    }
}
