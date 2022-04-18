<?php

namespace Amasty\Amp\Test\Unit\Controller;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Controller\Router;

/**
 * Class RouterTest
 *
 * @see Router
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RouterTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Router::match
     * @dataProvider matchDataProvider
     * @throws \ReflectionException
     */
    public function testMatch($identifier, $result)
    {
        $configProvider = $this->createMock(\Amasty\Amp\Model\ConfigProvider::class);
        $url = $this->createMock(\Magento\Backend\Model\UrlInterface::class);
        $url->expects($this->once())->method('getAreaFrontName')->willReturn('admin');
        $request = $this->getObjectManager()->getObject(\Magento\Framework\App\Request\Http::class);
        $controller = $this->getObjectManager()->getObject(
            Router::class,
            ['config' => $configProvider, 'url' => $url]
        );

        $request->setPathInfo($identifier);
        $controller->match($request);

        $this->assertEquals($result, $request->getPathInfo());
    }

    /**
     * Data provider for match test
     * @return array
     */
    public function matchDataProvider()
    {
        return [['test', 'test'], ['test/amp/test', 'test/test']];
    }
}
