<?php

namespace Amasty\Amp\Test\Unit\Observer;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Observer\LayoutLoadBefore;

/**
 * Class LayoutLoadBeforeTest
 *
 * @see LayoutLoadBefore
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class LayoutLoadBeforeTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers LayoutLoadBefore::execute
     * @dataProvider executeDataProvider
     * @throws \ReflectionException
     */
    public function testExecute($handle, $result)
    {
        $observerMock = $this->createPartialMock(
            \Magento\Framework\Event\Observer::class,
            ['getData', 'getEvent', 'getLayout', 'getUpdate', 'getHandles']
        );
        $config = $this->createMock(\Amasty\Amp\Model\ConfigProvider::class);
        $update = $this->getObjectManager()->getObject(\Magento\Framework\View\Model\Layout\Merge::class);
        $productMetadata = $this->createMock(\Magento\Framework\App\ProductMetadataInterface::class);
        $observer = $this->getObjectManager()->getObject(
            LayoutLoadBefore::class,
            [
                'productMetadata' => $productMetadata,
                'config' => $config
            ]
        );

        $observerMock->expects($this->any())->method('getData')->willReturn($handle);
        $observerMock->expects($this->any())->method('getEvent')->willReturn($observerMock);
        $observerMock->expects($this->any())->method('getLayout')->willReturn($observerMock);
        $observerMock->expects($this->any())->method('getUpdate')->willReturn($update);
        $productMetadata->expects($this->any())->method('getEdition')->willReturn('Community');
        $config->expects($this->any())->method('isAmpProductPage')->willReturn(true);

        $update->addHandle(['default', LayoutLoadBefore::CATALOG_PRODUCT_VIEW]);

        $observer->execute($observerMock);
        $this->assertEquals($result, $update->getHandles());
    }

    /**
     * Data provider for execute test
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [LayoutLoadBefore::CATALOG_PRODUCT_VIEW, ['amasty_amp_default', 'amasty_amp_ce_catalog_product_view']],
            ['test', ['default', LayoutLoadBefore::CATALOG_PRODUCT_VIEW]],
        ];
    }
}
