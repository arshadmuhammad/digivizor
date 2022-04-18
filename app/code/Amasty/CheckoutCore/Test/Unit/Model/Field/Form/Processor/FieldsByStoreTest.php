<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Test\Unit\Model\Field\Form\Processor;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\Form\Processor\FieldsByStore;
use Amasty\CheckoutCore\Model\Field\Form\SaveField;
use Amasty\CheckoutCore\Model\FieldFactory;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\Field\Collection;
use Amasty\CheckoutCore\Model\ResourceModel\Field\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @see FieldsByStore
 * @covers FieldsByStore::process
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class FieldsByStoreTest extends \PHPUnit\Framework\TestCase
{
    private const DEFAULT_STORE_ID = Field::DEFAULT_STORE_ID;
    private const STORE_ID = 1;
    private const ATTRIBUTE_ID = 1;

    private const ENABLED_NO = '0';
    private const ENABLED_YES = '1';

    /**
     * @var Collection|MockObject
     */
    private $fieldCollectionMock;

    /**
     * @var Collection|MockObject
     */
    private $defaultFieldCollectionMock;

    /**
     * @var SaveField|MockObject
     */
    private $saveFieldMock;

    /**
     * @var FieldFactory|MockObject
     */
    private $fieldFactoryMock;

    /**
     * @var FieldResource|MockObject
     */
    private $fieldResourceMock;

    /**
     * @var FieldsByStore
     */
    private $subject;

    protected function setUp(): void
    {
        $this->fieldCollectionMock = $this->createMock(Collection::class);
        $this->defaultFieldCollectionMock = $this->createMock(Collection::class);
        $fieldCollectionFactoryMock = $this->createMock(CollectionFactory::class);
        $fieldCollectionFactoryMock
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->fieldCollectionMock,
                $this->defaultFieldCollectionMock
            );

        $this->saveFieldMock = $this->createMock(SaveField::class);
        $this->fieldFactoryMock = $this->createMock(FieldFactory::class);
        $this->fieldResourceMock = $this->createMock(FieldResource::class);

        $this->subject = new FieldsByStore(
            $fieldCollectionFactoryMock,
            $this->saveFieldMock,
            $this->fieldFactoryMock,
            $this->fieldResourceMock
        );
    }

    public function testProcessWithNoFields(): void
    {
        $this->fieldCollectionMock->expects($this->never())->method('addFilterByStoreId');
        $this->fieldCollectionMock->expects($this->never())->method('getItemByColumnValue');
        $this->defaultFieldCollectionMock->expects($this->never())->method('addFilterByStoreId');
        $this->defaultFieldCollectionMock->expects($this->never())->method('getItemByColumnValue');
        $this->saveFieldMock->expects($this->never())->method('execute');
        $this->fieldFactoryMock->expects($this->never())->method('create');
        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->assertEquals([], $this->subject->process([], self::STORE_ID));
    }

    public function testProcessWithDefaultStoreId(): void
    {
        $fields = [self::ATTRIBUTE_ID => ['attribute_id' => self::ATTRIBUTE_ID]];

        $this->fieldCollectionMock->expects($this->never())->method('addFilterByStoreId');
        $this->fieldCollectionMock->expects($this->never())->method('getItemByColumnValue');
        $this->defaultFieldCollectionMock->expects($this->never())->method('addFilterByStoreId');
        $this->defaultFieldCollectionMock->expects($this->never())->method('getItemByColumnValue');
        $this->saveFieldMock->expects($this->never())->method('execute');
        $this->fieldFactoryMock->expects($this->never())->method('create');
        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->assertEquals($fields, $this->subject->process($fields, self::DEFAULT_STORE_ID));
    }

    public function testProcessWithoutUseDefault(): void
    {
        $fieldData = ['attribute_id' => self::ATTRIBUTE_ID];

        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(self::STORE_ID);
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn(null);

        $defaultFieldMock = $this->createMock(Field::class);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(Field::DEFAULT_STORE_ID);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($defaultFieldMock);

        $fieldMock = $this->createMock(Field::class);
        $this->fieldFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($fieldMock);

        $this->saveFieldMock
            ->expects($this->once())
            ->method('execute')
            ->with($fieldMock, [
                'attribute_id'  => self::ATTRIBUTE_ID,
                'required'      => false,
                'store_id'      => self::STORE_ID
            ]);

        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->assertEquals([], $this->subject->process([self::ATTRIBUTE_ID => $fieldData], self::STORE_ID));
    }

    public function testProcessWithoutUseDefaultWithField(): void
    {
        $fieldData = ['attribute_id' => self::ATTRIBUTE_ID];
        $fieldMock = $this->createMock(Field::class);

        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(self::STORE_ID);
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($fieldMock);

        $defaultFieldMock = $this->createMock(Field::class);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(Field::DEFAULT_STORE_ID);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($defaultFieldMock);

        $this->saveFieldMock
            ->expects($this->once())
            ->method('execute')
            ->with($fieldMock, [
                'attribute_id'  => self::ATTRIBUTE_ID,
                'required'      => false,
                'store_id'      => self::STORE_ID
            ]);

        $this->fieldFactoryMock->expects($this->never())->method('create');
        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->assertEquals([], $this->subject->process([self::ATTRIBUTE_ID => $fieldData], self::STORE_ID));
    }

    /**
     * @param array $fieldData
     * @param array $resultFieldData
     * @param MockObject $defaultFieldMock
     * @dataProvider processWithUseDefaultDataProvider
     */
    public function testProcessWithUseDefault(
        array $fieldData,
        array $resultFieldData,
        MockObject $defaultFieldMock
    ): void {
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(self::STORE_ID);
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn(null);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(Field::DEFAULT_STORE_ID);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($defaultFieldMock);

        $fieldMock = $this->createMock(Field::class);
        $this->fieldFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($fieldMock);

        $this->saveFieldMock
            ->expects($this->once())
            ->method('execute')
            ->with($fieldMock, $resultFieldData);

        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->assertEquals([], $this->subject->process([self::ATTRIBUTE_ID => $fieldData], self::STORE_ID));
    }

    /**
     * @param array $fieldData
     * @param MockObject $defaultFieldMock
     * @dataProvider processWithUseDefaultWithSameValuesForEnabledDataProvider
     */
    public function testProcessWithUseDefaultWithSameValuesForEnabled(
        array $fieldData,
        MockObject $defaultFieldMock
    ): void {
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(self::STORE_ID);
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn(null);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(Field::DEFAULT_STORE_ID);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($defaultFieldMock);

        $this->fieldFactoryMock->expects($this->never())->method('create');
        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->saveFieldMock->expects($this->never())->method('execute');
        $this->assertEquals([], $this->subject->process([self::ATTRIBUTE_ID => $fieldData], self::STORE_ID));
    }

    /**
     * @param array $fieldData
     * @param array $resultFieldData
     * @param MockObject $fieldMock
     * @dataProvider processWithUseDefaultDataProvider
     */
    public function testProcessWithUseDefaultWithField(
        array $fieldData,
        array $resultFieldData,
        MockObject $fieldMock
    ): void {
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(self::STORE_ID);
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($fieldMock);

        $defaultFieldMock = $this->createMock(Field::class);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(Field::DEFAULT_STORE_ID);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($defaultFieldMock);

        $this->saveFieldMock
            ->expects($this->once())
            ->method('execute')
            ->with($fieldMock, $resultFieldData);

        $this->fieldFactoryMock->expects($this->never())->method('create');
        $this->fieldResourceMock->expects($this->never())->method('delete');
        $this->assertEquals([], $this->subject->process([self::ATTRIBUTE_ID => $fieldData], self::STORE_ID));
    }

    /**
     * @param array $fieldData
     * @param MockObject $fieldMock
     * @dataProvider processWithUseDefaultWithSameValuesForEnabledDataProvider
     */
    public function testProcessWithUseDefaultWithFieldWithSameValuesForEnabled(
        array $fieldData,
        MockObject $fieldMock
    ): void {
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(self::STORE_ID);
        $this->fieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($fieldMock);

        $defaultFieldMock = $this->createMock(Field::class);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('addFilterByStoreId')
            ->with(Field::DEFAULT_STORE_ID);
        $this->defaultFieldCollectionMock
            ->expects($this->once())
            ->method('getItemByColumnValue')
            ->with(Field::ATTRIBUTE_ID, self::ATTRIBUTE_ID)
            ->willReturn($defaultFieldMock);

        $this->fieldResourceMock
            ->expects($this->once())
            ->method('delete')
            ->with($fieldMock);

        $this->fieldFactoryMock->expects($this->never())->method('create');
        $this->saveFieldMock->expects($this->never())->method('execute');
        $this->assertEquals([], $this->subject->process([self::ATTRIBUTE_ID => $fieldData], self::STORE_ID));
    }

    public function processWithUseDefaultDataProvider(): array
    {
        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())
            ->method('getData')
            ->with(Field::ENABLED, null)
            ->willReturn(self::ENABLED_YES);

        $data = [];
        $data[] = [
            ['enabled' => self::ENABLED_NO, 'required' => 1, 'use_default' => 1],
            [
                'attribute_id'  => self::ATTRIBUTE_ID,
                'required'      => true,
                'enabled'       => self::ENABLED_NO,
                'store_id'      => self::STORE_ID,
                'use_default'   => 1
            ],
            $fieldMock
        ];

        $fieldMock = $this->createMock(Field::class);
        $fieldMock->expects($this->once())
            ->method('getData')
            ->with(Field::ENABLED, null)
            ->willReturn(self::ENABLED_NO);

        $data[] = [
            ['enabled' => self::ENABLED_YES, 'required' => 1, 'use_default' => 1],
            [
                'attribute_id'  => self::ATTRIBUTE_ID,
                'required'      => true,
                'enabled'       => self::ENABLED_YES,
                'store_id'      => self::STORE_ID,
                'use_default'   => 1
            ],
            $fieldMock
        ];

        return $data;
    }

    public function processWithUseDefaultWithSameValuesForEnabledDataProvider(): array
    {
        $defaultFieldMock = $this->createMock(Field::class);
        $defaultFieldMock->expects($this->once())
            ->method('getData')
            ->with(Field::ENABLED, null)
            ->willReturn(self::ENABLED_YES);

        $data = [];
        $data[] = [
            ['enabled' => self::ENABLED_YES, 'required' => 1, 'use_default' => 1],
            $defaultFieldMock
        ];

        $defaultFieldMock = $this->createMock(Field::class);
        $defaultFieldMock->expects($this->once())
            ->method('getData')
            ->with(Field::ENABLED, null)
            ->willReturn(self::ENABLED_NO);

        $data[] = [
            ['enabled' => self::ENABLED_NO, 'required' => 1, 'use_default' => 1],
            $defaultFieldMock
        ];

        return $data;
    }
}
