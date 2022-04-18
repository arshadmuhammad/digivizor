<?php

namespace Amasty\Amp\Test\Unit\Block\Product\Content\View;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Block\Product\Content\View\Description;
use Magento\Catalog\Model\Product;
use Amasty\Amp\Helper\Catalog\Output;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class DescriptionTest
 *
 * @see Description
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class DescriptionTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    private $block;

    private $product;

    protected function setUp(): void
    {
        $this->block = $this->getObjectManager()->getObject(Description::class);
        $this->product = $this->createPartialMock(
            Product::class,
            ['getAttributeText', 'getResource']
        );
        $this->setProperty($this->block, '_product', $this->product, Description::class);
    }

    /**
     * @covers Description::getAttributeValue
     * @dataProvider getAttributeValueDataProvider
     * @throws \ReflectionException
     */
    public function testGetAttributeValue($type, $attribute, $result)
    {
        $helper = $this->createPartialMock(Output::class, ['productAttribute']);
        $this->block->setAtType($type);
        $this->block->setData('at_call', 'getResult');
        $this->block->setData('outputHelper', $helper);
        $this->product->setData('result', 'test');

        $helper->expects($this->any())->method('productAttribute')->willReturn($attribute);
        $this->product->expects($this->any())->method('getAttributeText')->willReturn('attributeText');

        $this->assertEquals($result, $this->block->getAttributeValue());
    }

    /**
     * Data provider for getAttributeValue test
     * @return array
     */
    public function getAttributeValueDataProvider()
    {
        return [
            ['text', false, ''],
            ['text', true, 'attributeText'],
            ['test', 'attribute', 'attribute'],
            ['', 'attribute', 'attribute'],
        ];
    }

    /**
     * @covers Description::getAttributeLabel
     * @dataProvider getAttributeLabelDataProvider
     * @throws \ReflectionException
     */
    public function testGetAttributeLabel($label, $storeLabel, $result)
    {
        $resource = $this->getMockBuilder(AbstractDb:: class)
            ->setMethods(['getAttribute', 'getStoreLabel'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->block->setAtLabel($label);

        $this->product->expects($this->any())->method('getResource')->willReturn($resource);
        $resource->expects($this->any())->method('getAttribute')->willReturn($resource);
        $resource->expects($this->any())->method('getStoreLabel')->willReturn($storeLabel);

        $this->assertEquals($result, $this->block->getAttributeLabel());
    }

    /**
     * Data provider for getAttributeLabel test
     * @return array
     */
    public function getAttributeLabelDataProvider()
    {
        return [
            ['', 'test', ''],
            ['default', 'test', 'test'],
            ['none', 'test', ''],
        ];
    }
}
