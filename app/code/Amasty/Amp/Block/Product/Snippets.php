<?php

namespace Amasty\Amp\Block\Product;

use Magento\Framework\View\Element\Template;

class Snippets extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Amp\Block\Page\Html\Header\Logo
     */
    private $logo;

    /**
     * @var \Magento\Theme\Block\Html\Title
     */
    private $title;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    private $catalogHelper;

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var \Magento\Review\Block\Product\ReviewRenderer
     */
    private $reviewRenderer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Amp\Model\Product\Review\ReviewSummary
     */
    private $reviewSummary;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Theme\Model\Favicon\Favicon $favicon,
        \Amasty\Amp\Block\Page\Html\Header\Logo $logo,
        \Magento\Theme\Block\Html\Title $title,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Amasty\Amp\Model\Product\Review\ReviewSummary $reviewSummary,
        array $data = []
    ) {
        $this->logo = $logo;
        $this->title = $title;
        $this->catalogHelper = $catalogHelper;
        $this->reviewRenderer = $reviewRenderer;
        $this->reviewFactory = $reviewFactory;
        $this->reviewSummary = $reviewSummary;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->stripTags($this->pageConfig->getDescription());
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logo->getLogoUrl();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title->getPageHeading();
    }

    /**
     * @return string|null
     */
    public function getSku()
    {
        $product = $this->catalogHelper->getProduct();

        return $product ? $product->getSku() : null;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReviewCount()
    {
        $product = $this->catalogHelper->getProduct();
        $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        $this->reviewRenderer->setProduct($product);

        return $this->reviewRenderer->getReviewsCount();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatingValue()
    {
        $product = $this->catalogHelper->getProduct();
        $storeId = $this->_storeManager->getStore()->getId();
        $isClassExist = $this->reviewSummary->appendSummaryDataToObject($product, $storeId);
        $ratingSummary = $isClassExist === false
            ? $this->getRatingSummaryForOldVersion($product, $storeId)
            : $product->getRatingSummary();

        return sprintf("%0.1f", $ratingSummary / 100 * 5);
    }

    /**
     * Compatibility with magento less than 2.3.3
     * @param $product
     * @param $storeId
     * @return string
     */
    private function getRatingSummaryForOldVersion($product, $storeId)
    {
        $this->reviewFactory->create()->getEntitySummary($product, $storeId);
        $this->reviewRenderer->setProduct($product);
        $ratingSummary = $this->reviewRenderer->getRatingSummary();

        return $ratingSummary;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->catalogHelper->getProduct()
            ->getPriceInfo()
            ->getPrice('final_price')
            ->getAmount()
            ->getValue();
    }
}
