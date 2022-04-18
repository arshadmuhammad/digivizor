<?php

namespace Amasty\Amp\Controller\Review;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\Amp\Block\Product\Content\View\Review\ListView;
use Magento\Framework\UrlInterface;

class ReviewsGetter
{
    const PAGE_SIZE = 4;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        TimezoneInterface $timezone
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getResponseContent($productId, $page)
    {
        $collection = $this->collectionFactory->create()
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->addEntityFilter('product', $productId)
            ->setDateOrder()
            ->setCurPage($page)
            ->setPageSize(self::PAGE_SIZE)
            ->addRateVotes();

        return $this->prepareResponseContent($collection, $productId, $page);
    }

    /**
     * @param $collection
     * @param $productId
     * @param $page
     * @return array
     */
    private function prepareResponseContent($collection, $productId, $page)
    {
        $responseContent = ['items' => []];
        foreach ($collection as $item) {
            $item->setCreatedAt($this->timezone->formatDateTime($item->getCreatedAt(), \IntlDateFormatter::LONG));
            $item->setRatingVotes($this->prepareRatingVotes($item));
            $responseContent['items'][] = $item->getData();
        }
        if ($page < $collection->getLastPageNumber()) {
            $responseContent['next'] = $this->urlBuilder->getBaseUrl() . ListView::AMASTY_AMP_REVIEW_LIST_AJAX
                . 'productId/' . $productId . '?p=' . ($page + 1);
        }

        return $responseContent;
    }

    /**
     * @param $review
     * @return array
     */
    private function prepareRatingVotes($review)
    {
        $result = [];
        foreach ($review->getRatingVotes() as $ratingVote) {
            $result[] = $ratingVote->getData();
        }

        return $result;
    }
}
