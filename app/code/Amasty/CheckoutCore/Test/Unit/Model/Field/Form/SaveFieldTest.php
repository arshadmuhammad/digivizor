<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Test\Unit\Model\Field\Form;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\Form\SaveField;
use Amasty\CheckoutCore\Model\Field\IsCustomFieldAttribute;
use Amasty\CheckoutCore\Model\Field\SetAttributeFrontendLabel;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\GetCustomerAddressAttributeById;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @see SaveField
 * @covers SaveField::execute
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SaveFieldTest extends \PHPUnit\Framework\TestCase
{
    private const ATTRIBUTE_ID = '42';

    /**
     * @var FieldResource|MockObject
     */
    private $fieldResourceMock;

    /**
     * @var IsCustomFieldAttribute|MockObject
     */
    private $isCustomFieldAttributeMock;

    /**
     * @var GetCustomerAddressAttributeById|MockObject
     */
    private $getCustomerAddressAttributeByIdMock;

    /**
     * @var SetAttributeFrontendLabel|MockObject
     */
    private $setAttributeFrontendLabelMock;

    /**
     * @var AttributeResource|MockObject
     */
    private $attributeResourceMock;

    protected function setUp(): void
    {
        $this->fieldResourceMock = $this->createMock(FieldResource::class);
        $this->isCustomFieldAttributeMock = $this->createMock(IsCustomFieldAttribute::class);
        $this->getCustomerAddressAttributeByIdMock = $this->createMock(GetCustomerAddressAttributeById::class);
        $this->setAttributeFrontendLabelMock = $this->createMock(SetAttributeFrontendLabel::class);
        $this->attributeResourceMock = $this->createMock(AttributeResource::class);
    }

    public function testExecuteNoAllowedKeys(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No keys were allowed');

        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->never())->method('addData');
        $this->fieldResourceMock->expects($this->never())->method('save');
        $this->isCustomFieldAttributeMock->expects($this->never())->method('execute');
        $this->getCustomerAddressAttributeByIdMock->expects($this->never())->method('execute');
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->isCustomFieldAttributeMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->setAttributeFrontendLabelMock,
            $this->attributeResourceMock,
            []
        );

        $subject->execute($fieldMock, ['attribute_id' => self::ATTRIBUTE_ID]);
    }

    public function testExecuteWithNoFieldData(): void
    {
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->never())->method('addData');
        $this->fieldResourceMock->expects($this->never())->method('save');
        $this->isCustomFieldAttributeMock->expects($this->never())->method('execute');
        $this->getCustomerAddressAttributeByIdMock->expects($this->never())->method('execute');
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->isCustomFieldAttributeMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->setAttributeFrontendLabelMock,
            $this->attributeResourceMock,
            ['some_key']
        );

        $subject->execute($fieldMock, []);
    }

    /**
     * @param array $fieldData
     * @param array $expectedDataToAdd
     * @param string[] $allowedKeys
     * @dataProvider executeWithFieldDataProvider
     */
    public function testExecuteWithFieldData(
        array $fieldData,
        array $expectedDataToAdd,
        array $allowedKeys
    ): void {
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())->method('addData')->with($expectedDataToAdd);

        $this->isCustomFieldAttributeMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) $fieldData['attribute_id'])
            ->willReturn(false);

        $this->fieldResourceMock->expects($this->once())->method('save')->with($fieldMock);
        $this->getCustomerAddressAttributeByIdMock->expects($this->never())->method('execute');
        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->isCustomFieldAttributeMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->setAttributeFrontendLabelMock,
            $this->attributeResourceMock,
            $allowedKeys
        );

        $subject->execute($fieldMock, $fieldData);
    }

    /**
     * @param array $fieldData
     * @param array $expectedDataToAdd
     * @param string[] $allowedKeys
     * @dataProvider executeWithFieldDataProvider
     */
    public function testExecuteWithCustomFieldAndNoAttribute(
        array $fieldData,
        array $expectedDataToAdd,
        array $allowedKeys
    ): void {
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())->method('addData')->with($expectedDataToAdd);

        $this->isCustomFieldAttributeMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) $fieldData['attribute_id'])
            ->willReturn(true);

        $this->fieldResourceMock->expects($this->once())->method('save')->with($fieldMock);
        $this->getCustomerAddressAttributeByIdMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) $fieldData['attribute_id'])
            ->willReturn(null);

        $this->setAttributeFrontendLabelMock->expects($this->never())->method('execute');
        $this->attributeResourceMock->expects($this->never())->method('save');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->isCustomFieldAttributeMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->setAttributeFrontendLabelMock,
            $this->attributeResourceMock,
            $allowedKeys
        );

        $subject->execute($fieldMock, $fieldData);
    }

    /**
     * @param array $fieldData
     * @param array $expectedDataToAdd
     * @param string[] $allowedKeys
     * @dataProvider executeWithFieldDataProvider
     */
    public function testExecuteWithCustomField(
        array $fieldData,
        array $expectedDataToAdd,
        array $allowedKeys
    ): void {
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())->method('addData')->with($expectedDataToAdd);

        $this->isCustomFieldAttributeMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) $fieldData['attribute_id'])
            ->willReturn(true);

        $this->fieldResourceMock->expects($this->once())->method('save')->with($fieldMock);

        $attributeMock = $this->createMock(Attribute::class);

        $this->getCustomerAddressAttributeByIdMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) $fieldData['attribute_id'])
            ->willReturn($attributeMock);

        $this->setAttributeFrontendLabelMock
            ->expects($this->once())
            ->method('execute')
            ->with($attributeMock, (int) $fieldData['store_id'], $fieldData['label']);

        $this->attributeResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($attributeMock);

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->isCustomFieldAttributeMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->setAttributeFrontendLabelMock,
            $this->attributeResourceMock,
            $allowedKeys
        );

        $subject->execute($fieldMock, $fieldData);
    }

    public function executeWithFieldDataProvider(): array
    {
        return [
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 0,
                    'enabled'       => 1,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => '1'
                ],
                [
                    'attribute_id' => self::ATTRIBUTE_ID,
                    'enabled' => 1,
                    'label' => 'Test',
                ],
                ['attribute_id', 'enabled', 'label']
            ]
        ];
    }
}
