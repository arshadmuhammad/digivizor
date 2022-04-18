<?php

namespace Amasty\Amp\Plugin\Review\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\Session;
use Magento\Framework\Message\Manager;

class PostPlugin
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

    public function __construct(
        \Amasty\Amp\Model\UrlConfigProvider $urlConfigProvider,
        \Amasty\Amp\Model\ConfigProvider $configProvider,
        Session $session,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->urlConfigProvider = $urlConfigProvider;
        $this->session = $session;
        $this->json = $json;
        $this->configProvider = $configProvider;
    }

    /**
     * @param Action $controller
     * @param $result
     * @return void|$result
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterExecute(Action $controller, $result)
    {
        if ($this->configProvider->isAmpProductPage()) {
            $this->urlConfigProvider->addAmpHeaders($controller->getResponse());
            $controller->getResponse()->setBody($this->prepareMessage());
        } else {
            return $result;
        }
    }

    /**
     * @return string
     */
    private function prepareMessage()
    {
        $messages = $this->session->getData(Manager::DEFAULT_GROUP);
        $this->session->setData(Manager::DEFAULT_GROUP, null);

        $message = $messages->getLastAddedMessage();
        $type = $this->configProvider->getMessageType($message);
        $resultMessage = $this->json->serialize([
            $type => $message ? htmlspecialchars_decode($message->getText(), ENT_QUOTES) : ''
        ]);

        return $resultMessage;
    }
}
