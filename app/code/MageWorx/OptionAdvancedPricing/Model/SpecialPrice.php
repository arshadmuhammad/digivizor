<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionAdvancedPricing\Model;

use Magento\Catalog\Model\Product\Option\Value as OptionValue;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use MageWorx\OptionBase\Helper\CustomerVisibility as CustomerVisibilityHelper;
use MageWorx\OptionAdvancedPricing\Helper\Data as Helper;

class SpecialPrice extends AbstractModel
{
    const TABLE_NAME                 = 'mageworx_optionadvancedpricing_option_type_special_price';
    const OPTIONTEMPLATES_TABLE_NAME = 'mageworx_optiontemplates_group_option_type_special_price';

    const COLUMN_OPTION_TYPE_SPECIAL_PRICE_ID = 'option_type_special_price_id';
    const COLUMN_MAGEWORX_OPTION_TYPE_ID      = 'mageworx_option_type_id';
    const COLUMN_OPTION_TYPE_ID               = 'option_type_id';
    const COLUMN_CUSTOMER_GROUP_ID            = 'customer_group_id';
    const COLUMN_PRICE                        = 'price';
    const COLUMN_PRICE_TYPE                   = 'price_type';
    const COLUMN_COMMENT                      = 'comment';
    const COLUMN_DATE_FROM                    = 'date_from';
    const COLUMN_DATE_TO                      = 'date_to';

    const FIELD_OPTION_TYPE_ID_ALIAS = 'mageworx_special_price_option_type_id';
    const KEY_SPECIAL_PRICE          = 'special_price';

    /**
     * @var CustomerVisibilityHelper
     */
    protected $customerVisibilityHelper;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var ConditionValidator
     */
    protected $conditionValidator;

    /**
     * @var array
     */
    protected $activeSpecialPriceItem;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * SpecialPrice constructor.
     *
     * @param CustomerVisibilityHelper $customerVisibilityHelper
     * @param Helper $helper
     * @param ConditionValidator $conditionValidator
     * @param Context $context
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param Serializer $serializer
     */
    public function __construct(
        CustomerVisibilityHelper $customerVisibilityHelper,
        Helper $helper,
        ConditionValidator $conditionValidator,
        Context $context,
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Serializer $serializer
    ) {
        $this->customerVisibilityHelper = $customerVisibilityHelper;
        $this->helper                   = $helper;
        $this->conditionValidator       = $conditionValidator;
        $this->priceCurrency            = $priceCurrency;
        $this->serializer               = $serializer;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\OptionAdvancedPricing\Model\ResourceModel\SpecialPrice');
        $this->setIdFieldName(self::COLUMN_OPTION_TYPE_SPECIAL_PRICE_ID);
    }

    /**
     * Get actual special price according to date and customer group
     *
     * @param OptionValue $optionValue
     * @param bool $isNeedConvert
     * @return float|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getActualSpecialPrice(OptionValue $optionValue, $isNeedConvert = false)
    {
        $actualPrice = null;
        if (!$this->helper->isSpecialPriceEnabled()) {
            return $actualPrice;
        }
        $specialPricesJson = $optionValue->getData(static::KEY_SPECIAL_PRICE);
        if (!$specialPricesJson) {
            return $actualPrice;
        }
        $specialPrices = $this->serializer->unserialize($specialPricesJson);
        if (!$specialPrices) {
            return $actualPrice;
        }

        $valuePrice               = $optionValue->getPrice(true);
        $currentCustomerGroupId   = $this->customerVisibilityHelper->getCurrentCustomerGroupId();
        $directCustomerGroupPrice = null;
        $allCustomerGroupPrice    = null;
        $allCustomersGroupId      = $this->customerVisibilityHelper->getAllCustomersGroupId();
        foreach ($specialPrices as $specialPriceItem) {

            if (!in_array($specialPriceItem['customer_group_id'], [$currentCustomerGroupId, $allCustomersGroupId])) {
                continue;
            }

            if ($specialPriceItem['price_type'] == Helper::PRICE_TYPE_PERCENTAGE_DISCOUNT) {
                $specialPriceItem['price']      = $this->helper->getCalculatedPriceWithPercentageDiscount(
                    $optionValue,
                    $specialPriceItem
                );
                $specialPriceItem['price_type'] = Helper::PRICE_TYPE_FIXED;
            }

            if (!$this->conditionValidator->isValidated($specialPriceItem, $valuePrice)) {
                continue;
            }

            if ($specialPriceItem['customer_group_id'] == $currentCustomerGroupId) {
                $this->activeSpecialPriceItem = $specialPriceItem;

                return $isNeedConvert
                    ? $this->priceCurrency->convert($specialPriceItem['price'])
                    : $specialPriceItem['price'];
            }

            if ($specialPriceItem['customer_group_id'] == $allCustomersGroupId) {
                $priceItemForAllCustomerGroup = $specialPriceItem;
            }
        }

        if (!empty($priceItemForAllCustomerGroup)) {
            $this->activeSpecialPriceItem = $priceItemForAllCustomerGroup;

            return $isNeedConvert
                ? $this->priceCurrency->convert($priceItemForAllCustomerGroup['price'])
                : $priceItemForAllCustomerGroup['price'];
        }

        return null;
    }

    /**
     * Get active special price item
     *
     * @return array
     */
    public function getSpecialPriceItem()
    {
        return $this->activeSpecialPriceItem;
    }
}
