<?php

namespace Amasty\Amp\Block\Product\Content\View;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer implements ArgumentInterface
{
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'Amasty_Amp::components/rating_view.phtml',
    ];

    /**
     * @return string
     */
    public function getRatingSummary()
    {
        $summary = $this->getProduct()->getRatingSummary();

        return is_string($summary) ? $summary : $summary->getRatingSummary(); //fix for 2.3.3
    }

    /**
     * @param $reviewsCount
     * @return string
     */
    public function getReviewLabel($reviewsCount)
    {
        return ($reviewsCount == 1) ? __('Review') : __('Reviews');
    }

    /**
     * @return bool
     */
    public function isProductPage()
    {
        return $this->_request->getFullActionName() == 'catalog_product_view';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $result = true;
        if (method_exists($this, 'isReviewEnabled')) {
            $result = $this->isReviewEnabled();
        }

        return $result;
    }
}
