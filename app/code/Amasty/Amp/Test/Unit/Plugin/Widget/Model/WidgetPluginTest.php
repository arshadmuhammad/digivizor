<?php

namespace Amasty\Amp\Test\Unit\Plugin\Review\Controller\Product;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Plugin\Widget\Model\WidgetPlugin;

/**
 * Class WidgetPluginTest
 *
 * @see WidgetPlugin
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class WidgetPluginTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers WidgetPlugin::afterGetWidgets
     * @dataProvider afterGetWidgetsDataProvider
     * @throws \ReflectionException
     */
    public function testAfterGetWidgets($widgets, $param, $result)
    {
        $request = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $subject = $this->createMock(\Magento\Widget\Model\Widget::class);
        $model = $this->getObjectManager()->getObject(
            WidgetPlugin::class,
            [
                'request' => $request
            ]
        );

        $request->expects($this->any())->method('getParam')->willReturnCallback(
            function ($id) use ($param) {
                return $param;
            }
        );

        $this->assertEquals($result, $model->afterGetWidgets($subject, $widgets));
    }

    /**
     * Data provider for afterGetWidgets test
     * @return array
     */
    public function afterGetWidgetsDataProvider()
    {
        $widgetsWithoutAmasty = ['test' => 'test', 'test1' => 'test1'];
        $widgetsWithAmasty = ['amasty_amp_test' => 'test', 'test1' => 'test1'];

        return [
            [$widgetsWithoutAmasty, 'test', $widgetsWithoutAmasty],
            [$widgetsWithoutAmasty, WidgetPlugin::CMS_PAGE_FORM_AMP_CONTENT, []],
            [$widgetsWithAmasty, WidgetPlugin::CMS_PAGE_FORM_AMP_CONTENT, ['amasty_amp_test' => 'test']],
        ];
    }
}
