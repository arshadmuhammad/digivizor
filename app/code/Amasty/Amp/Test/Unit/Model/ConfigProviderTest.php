<?php

namespace Amasty\Amp\Test\Unit\Model;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Model\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ConfigProviderTest
 *
 * @see ConfigProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers ConfigProvider::isAmpProductPage
     * @dataProvider isAmpProductPageDataProvider
     * @throws \ReflectionException
     */
    public function testisAmpProductPage($path, $params, $result)
    {
        $config = $this->createMock(ScopeConfigInterface::class);
        $request = $this->getObjectManager()->getObject(\Magento\Framework\App\Request\Http::class);
        $model = $this->getObjectManager()->getObject(
            ConfigProvider::class,
            [
                'scopeConfig' => $config,
                'request' => $request
            ]
        );

        $request->setParams($params);
        $this->setProperty($request, 'originalPathInfo', $path, \Magento\Framework\App\Request\Http::class);

        $config->expects($this->any())->method('getValue')->willReturn(true);

        $this->assertEquals($result, $model->isAmpProductPage());
    }

    /**
     * Data provider for isAmpProductPage test
     * @return array
     */
    public function isAmpProductPageDataProvider()
    {
        return [
            ['test', ['test' => 1], false],
            ['test', ['amp_page' => 1], true],
            ['test/amp/test', ['test' => 1], true],
            ['test/amp/test', ['amp_page' => 1], true]
        ];
    }
}
