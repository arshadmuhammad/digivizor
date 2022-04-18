<?php

namespace Amasty\Amp\Test\Unit\Plugin\Newsletter\Subscriber;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Plugin\Newsletter\Subscriber\NewActionPlugin;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\Session;

/**
 * Class NewActionPluginTest
 *
 * @see NewActionPlugin
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class NewActionPluginTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers NewActionPlugin::afterExecute
     * @throws \ReflectionException
     */
    public function testAfterExecute()
    {
        $configProvider = $this->createMock(\Amasty\Amp\Model\ConfigProvider::class);
        $urlConfigProvider = $this->createMock(\Amasty\Amp\Model\UrlConfigProvider ::class);
        $session = $this->createMock(Session::class);
        $json = $this->createMock(\Magento\Framework\Serialize\Serializer\Json::class);
        $messageCollection = $this->createPartialMock(\Magento\Framework\Message\Collection::class, ['getLastAddedMessage']);
        $message = $this->createPartialMock(\Magento\Framework\Message\Error::class, ['getText']);
        $controller = $this->getMockBuilder(Action::class)
            ->setMethods(['getResponse', 'setBody', 'getHttpResponseCode'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $plugin = $this->getObjectManager()->getObject(
            NewActionPlugin::class,
            [
                'configProvider' => $configProvider,
                'urlConfigProvider' => $urlConfigProvider,
                'json' => $json,
                'session' => $session
            ]
        );

        $configProvider->expects($this->any())->method('isAmpPage')->willReturnOnConsecutiveCalls(false, true);
        $configProvider->expects($this->once())->method('getMessageType')->willReturn('test');
        $session->expects($this->once())->method('getData')->willReturn($messageCollection);
        $messageCollection->expects($this->once())->method('getLastAddedMessage')->willReturn($message);
        $message->expects($this->once())->method('getText')->willReturn('test');
        $json->expects($this->once())->method('serialize');
        $urlConfigProvider->expects($this->once())->method('addAmpHeaders');
        $controller->expects($this->any())->method('getResponse')->willReturn($controller);

        $this->assertEquals('test', $plugin->afterExecute($controller, 'test'));
        $plugin->afterExecute($controller, 'test');
    }
}
