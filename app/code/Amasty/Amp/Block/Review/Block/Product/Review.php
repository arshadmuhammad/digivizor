<?php

namespace Amasty\Amp\Block\Review\Block\Product;

use Magento\Customer\Model\Context;

class Review extends \Magento\Review\Block\Product\Review
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $reviewData = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Review\Helper\Data $reviewData,
        array $data = []
    ) {
        parent::__construct($context, $registry, $collectionFactory, $data);
        $this->httpContext = $httpContext;
        $this->reviewData = $reviewData;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $enabled = $this->_scopeConfig->getValue("catalog/review/active");

        if ($enabled !== '0') { // setting doesn't exist on magento less 2.3.0 - should be true for null
            return parent::toHtml();
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isAllowWriteReview()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH)
            || $this->reviewData->getIsGuestAllowToWrite();
    }
}
