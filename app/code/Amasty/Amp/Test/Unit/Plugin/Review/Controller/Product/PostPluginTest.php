<?php

namespace Amasty\Amp\Test\Unit\Plugin\Review\Controller\Product;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Plugin\Review\Controller\Product\PostPlugin;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Message\Session;

/**
 * Class PostPluginTest
 *
 * @see PostPlugin
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class PostPluginTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers PostPlugin::afterExecute
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
            ->setMethods(['getResponse', 'setBody'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $plugin = $this->getObjectManager()->getObject(
            PostPlugin::class,
            [
                'configProvider' => $configProvider,
                'urlConfigProvider' => $urlConfigProvider,
                'json' => $json,
                'session' => $session
            ]
        );

        $configProvider->expects($this->any())->method('isAmpProductPage')->willReturnOnConsecutiveCalls(false, true);
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
