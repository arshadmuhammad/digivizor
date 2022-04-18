<?php

namespace Amasty\Amp\Plugin\Newsletter\Subscriber;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\Session;

class NewActionPlugin
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
     * @return mixed|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterExecute(Action $controller, $result)
    {
        if ($this->configProvider->isAmpPage()) {
            $messages = $this->session->getData(Manager::DEFAULT_GROUP);
            $this->session->setData(Manager::DEFAULT_GROUP, null);

            $message = $messages->getLastAddedMessage();
            $type = $this->configProvider->getMessageType($message);
            $response = $controller->getResponse();

            $this->urlConfigProvider->addAmpHeaders($response);
            $response->setBody($this->json->serialize([
                $type => $message ? htmlspecialchars_decode($message->getText(), ENT_QUOTES) : ''
            ]));

            if ($response->getHttpResponseCode() == 302) {
                $response->clearHeader('Location')->setHttpResponseCode(200);
            }

            return $response;
        } else {
            return $result;
        }
    }
}
