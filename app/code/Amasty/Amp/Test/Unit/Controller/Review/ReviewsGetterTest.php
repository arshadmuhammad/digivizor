<?php

namespace Amasty\Amp\Test\Unit\Controller\Review;

use Amasty\Amp\Test\Unit\Traits;
use Amasty\Amp\Controller\Review\ReviewsGetter;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\ResourceModel\Review\Collection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ReviewsGetterTest
 *
 * @see ReviewsGetter
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ReviewsGetterTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var ReviewsGetter
     */
    private $controller;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $collection;

    protected function setUp(): void
    {
        $collectionFactory = $this->createMock(CollectionFactory::class);
        $this->collection = $this->getMockBuilder(Collection::class)
            ->setMethods(
                [
                    'addStoreFilter',
                    'addStatusFilter',
                    'addEntityFilter',
                    'setDateOrder',
                    'setCurPage',
                    'setPageSize',
                    'addRateVotes',
                    'getIterator',
                    'getLastPageNumber'
                ]
            )
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $store = $this->createMock(\Magento\Store\Api\Data\StoreInterface::class);
        $timezone = $this->createMock(TimezoneInterface::class);
        $review = $this->getObjectManager()->getObject(DataObject::class, [
            'data' => ['test' => 'test', 'created_at' => '2.2.2']
        ]);
        $vote = $this->getObjectManager()->getObject(DataObject::class, [
            'data' => ['testVote' => 'testVote']
        ]);
        $this->controller = $this->getObjectManager()->getObject(
            ReviewsGetter::class,
            [
                'collectionFactory' => $collectionFactory,
                'storeManager' => $storeManager,
                'timezone' => $timezone,
            ]
        );

        $collectionFactory->expects($this->any())->method('create')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('addStoreFilter')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('addStatusFilter')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('addEntityFilter')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('setDateOrder')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('setCurPage')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('setPageSize')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('addRateVotes')->willReturn($this->collection);
        $this->collection->expects($this->any())->method('getIterator')->willReturn(new \ArrayIterator([$review]));
        $storeManager->expects($this->any())->method('getStore')->willReturn($store);
        $store->expects($this->any())->method('getId')->willReturn(5);
        $timezone->expects($this->any())->method('formatDateTime')->willReturn('1.1.1');

        $review->setRatingVotes([$vote]);
    }

    /**
     * @covers ReviewsGetter::getResponseContent
     * @dataProvider getResponseContentDataProvider
     * @throws \ReflectionException
     */
    public function testGetResponseContent($page, $result)
    {
        $this->collection->expects($this->any())->method('getLastPageNumber')->willReturn($page);
        $this->assertEquals($result, $this->controller->getResponseContent(2, 2));
    }

    /**
     * Data provider for getResponseContent test
     * @return array
     */
    public function getResponseContentDataProvider()
    {
        $items = [
            [
                'test' => 'test',
                'created_at' => '1.1.1',
                'rating_votes' => [['testVote' => 'testVote']]
            ]
        ];

        return [
            [1, ['items' => $items]],
            [3, ['next' => 'amasty_amp/review/listAjax/productId/2?p=3', 'items' => $items]],
        ];
    }
}
