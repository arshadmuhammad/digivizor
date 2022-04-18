<?php

/**
 * @category  Utility
 * @package   Utility_subcat
 */

namespace Utility\Subcat\ViewModel;

/**
 * Base view model class for helper operations
 */
class Base implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    /**
     * Catalog Category Helper
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $categoryHelper;

    /**
     *  Pricing Helper
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     *  Catalog Output Helper
     *
     * @var \Magento\Catalog\Helper\Output
     */
    protected $outputHelper;

    /**
     * Category Collection Factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryFactory;

    /**
     * Store Manager Interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->priceHelper = $priceHelper;
        $this->outputHelper = $outputHelper;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Load category using id
     *
     * @param intger $categoryId category id
     *
     * @return Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategoryById($categoryId)
    {
        $collection = $this->categoryFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['eq' => $categoryId])
            ->setPageSize(1);
        return $collection->getFirstItem();
    }

    /**
     * Retrieve category image src
     *
     * @param object $category Magento\Catalog\Model\Category
     *
     * @return string|null
     */
    public function getCategoryImageSrc(\Magento\Catalog\Model\Category $category)
    {
        if ($category->getImageUrl()) {
            if (strpos($category->getImageUrl(), 'http') !== false) {
                return $category->getImageUrl();
            } else {
                return $this->storeManager->getStore()->getBaseUrl() . $category->getImageUrl();
            }
        } else {
            return false;
        }
    }

    /**
     * Retrieve thumbnail Image path
     *
     * @param object $category Magento\Catalog\Model\Category Catalog model
     *
     * @return string
     */
    public function getCategoryUrl(\Magento\Catalog\Model\Category $category)
    {
        return $this->categoryHelper->getCategoryUrl($category);
    }
}
