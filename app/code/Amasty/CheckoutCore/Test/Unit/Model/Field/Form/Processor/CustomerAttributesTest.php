<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Test\Unit\Model\Field\Form\Processor;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\Form\GetCustomerAttributes;
use Amasty\CheckoutCore\Model\Field\Form\Processor\CustomerAttributes;
use Amasty\CheckoutCore\Model\Field\Form\SelectFormCodes;
use Amasty\CheckoutCore\Model\Field\SetAttributeFrontendLabel;
use Amasty\CheckoutCore\Model\ModuleEnable;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @see CustomerAttributes
 * @covers CustomerAttributes::process
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CustomerAttributesTest extends \PHPUnit\Framework\TestCase
{
    private const DEFAULT_STORE_ID = Field::DEFAULT_STORE_ID;
    private const STORE_ID = 1;

    /**
     * @var ModuleEnable|MockObject
     */
    private $moduleEnableMock;

    /**
     * @var GetCustomerAttributes|MockObject
     */
    private $getCustomerAttributesMock;

    /**
     * @var SetAttributeFrontendLabel|MockObject
     */
    private $setAttributeFrontendLabelMock;

    /**
     * @var SelectFormCodes|MockObject
     */
    private $selectFormCodesMock;

    /**
     * @var AttributeResource|MockObject
     */
    private $attributeResourceMock;

    /**
     * @var CustomerAttributes
     */
    private $subject;

    protected function setUp(): void
    {
        $this->moduleEnableMock = $this->createMock(ModuleEnable::class);
        $this->getCustomerAttributesMock = $this->createMock(GetCustomerAttributes::class);
        $this->setAttributeFrontendLabelMock = $this->createMock(SetAttributeFrontendLabel::class);
        $this->selectFormCodesMock = $this->createMock(SelectFormCodes::class);
        $this->attributeResourceMock = $this->createMock(AttributeResource::class);

        $this->subject = new CustomerAttributes(
            $this->moduleEnableMock,
            $this->getCustomerAttributesMock,
            $this->setAttributeFrontendLabelMock,
            $this->selectFormCodesMock,
            $this->attributeResourceMock
        );
    }

    public function testProcessWithModuleDisabled(): void
    {
        $dummyFieldData = ['attribute_id' => 1];

        $this->moduleEnableMock->expects($this->once())->method('isCustomerAttributesEnable')->willReturn(false);
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');
        $this->assertEquals([$dummyFieldData], $this->subject->process([$dummyFieldData], self::DEFAULT_STORE_ID));
    }

    public function testProcessWithNoFields(): void
    {
        $this->moduleEnableMock->expects($this->once())->method('isCustomerAttributesEnable')->willReturn(true);
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');
        $this->assertEquals([], $this->subject->process([], self::DEFAULT_STORE_ID));
    }

    public function testProcessWithoutAttributes(): void
    {
        $dummyFieldData = ['attribute_id' => 1];

        $this->moduleEnableMock->expects($this->once())->method('isCustomerAttributesEnable')->willReturn(true);
        $this->getCustomerAttributesMock->expects($this->once())->method('execute')->willReturn([]);
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');
        $this->assertEquals([$dummyFieldData], $this->subject->process([$dummyFieldData], self::DEFAULT_STORE_ID));
    }

    /**
     * @param MockObject $attributeMock
     * @param array $fields
     * @dataProvider processWithNoMatchingAttributesDataProvider
     */
    public function testProcessWithNoMatchingAttributes(MockObject $attributeMock, array $fields): void
    {
        $this->moduleEnableMock->expects($this->once())->method('isCustomerAttributesEnable')->willReturn(true);
        $this->getCustomerAttributesMock->expects($this->once())->method('execute')->willReturn([$attributeMock]);
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');
        $this->assertSame($fields, $this->subject->process($fields, self::DEFAULT_STORE_ID));
    }

    /**
     * @param MockObject $attributeMock
     * @param array $fields
     * @dataProvider processWithFieldWithUseDefaultDataProvider
     */
    public function testProcessWithFieldWithUseDefault(MockObject $attributeMock, array $fields): void
    {
        $this->moduleEnableMock->expects($this->once())->method('isCustomerAttributesEnable')->willReturn(true);
        $this->getCustomerAttributesMock->expects($this->once())->method('execute')->willReturn([$attributeMock]);
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');
        $this->assertEquals([], $this->subject->process($fields, self::DEFAULT_STORE_ID));
    }

    /**
     * @param MockObject $attributeMock
     * @param array $fields
     * @dataProvider processWithMatchingAttributeDataProvider
     */
    public function testProcessWithDefaultStoreId(MockObject $attributeMock, array $fields): void
    {
        $this->checkWithMatchingAttribute($attributeMock, $fields);

        $fieldData = reset($fields);

        $attributeMock
            ->expects($this->once())
            ->method('setIsRequired')
            ->with(isset($fieldData['required']));

        $this->setAttributeFrontendLabelMock
            ->expects($this->once())
            ->method('execute')
            ->with($attributeMock, self::DEFAULT_STORE_ID, $fieldData['label']);

        $this->assertEquals([], $this->subject->process($fields, self::DEFAULT_STORE_ID));
    }

    /**
     * @param MockObject $attributeMock
     * @param array $fields
     * @dataProvider processWithMatchingAttributeDataProvider
     */
    public function testProcessWithStoreId(MockObject $attributeMock, array $fields): void
    {
        $this->checkWithMatchingAttribute($attributeMock, $fields);

        $fieldData = reset($fields);

        $this->setAttributeFrontendLabelMock
            ->expects($this->once())
            ->method('execute')
            ->with($attributeMock, self::STORE_ID, $fieldData['label']);

        $attributeMock->expects($this->never())->method('setIsRequired');
        $this->assertEquals([], $this->subject->process($fields, self::STORE_ID));
    }

    public function processWithNoMatchingAttributesDataProvider(): array
    {
        return [
            [
                $this->createConfiguredMock(Attribute::class, ['getAttributeId' => '1']),
                [
                    2 => ['attribute_id' => 2]
                ]
            ]
        ];
    }

    public function processWithFieldWithUseDefaultDataProvider(): array
    {
        return [
            [
                $this->createConfiguredMock(Attribute::class, ['getAttributeId' => '1']),
                [
                    1 => ['attribute_id' => 1, 'use_default' => 1]
                ]
            ]
        ];
    }

    public function processWithMatchingAttributeDataProvider(): array
    {
        return [
            [
                $this->createConfiguredMock(Attribute::class, ['getAttributeId' => '1']),
                [
                    1 => ['attribute_id' => 1, 'enabled' => 0, 'sort_order' => 0, 'label' => 'test']
                ]
            ],
            [
                $this->createConfiguredMock(Attribute::class, ['getAttributeId' => '1']),
                [
                    1 => [
                        'attribute_id'  => 1,
                        'enabled'       => 1,
                        'sort_order'    => 0,
                        'required'      => 1,
                        'label'         => 'test'
                    ]
                ]
            ]
        ];
    }

    private function checkWithMatchingAttribute(MockObject $attributeMock, array $fields): void
    {
        $this->moduleEnableMock->expects($this->once())->method('isCustomerAttributesEnable')->willReturn(true);
        $this->getCustomerAttributesMock->expects($this->once())->method('execute')->willReturn([$attributeMock]);

        $fieldData = reset($fields);

        $formCodes = ['some_form', 'another_form'];
        $this->selectFormCodesMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn($formCodes);

        $attributeMock
            ->expects($this->exactly(4))
            ->method('setData')
            ->withConsecutive(
                ['sort_order', $fieldData['sort_order'] + 1000],
                ['used_in_product_listing', $fieldData['enabled']],
                ['used_in_forms', $formCodes],
                ['sorting_order', $fieldData['sort_order']]
            );

        $this->attributeResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($attributeMock);
    }
}
