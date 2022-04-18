<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Plugin;

use MageWorx\OptionBase\Model\ResourceModel\DataSaver;
use MageWorx\OptionBase\Model\ValidationResolver;
use Magento\Catalog\Model\Product\Option\ValueFactory as OptionValueFactory;

class IsRequireChecker
{
    /**
     * @var DataSaver
     */
    protected $dataSaver;

    /**
     * @var ValidationResolver
     */
    protected $validationResolver;

    /**
     * @var OptionValueFactory
     */
    protected $optionValueFactory;

    /**
     * IsRequireChecker constructor.
     *
     * @param ValidationResolver $validationResolver
     * @param OptionValueFactory $optionValueFactory
     * @param DataSaver $dataSaver
     */
    public function __construct(
        ValidationResolver $validationResolver,
        OptionValueFactory $optionValueFactory,
        DataSaver $dataSaver
    ) {
        $this->validationResolver = $validationResolver;
        $this->optionValueFactory = $optionValueFactory;
        $this->dataSaver          = $dataSaver;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function afterAfterSave(
        \Magento\Catalog\Model\Product $subject,
        $product
    ) {
        $options          = $product->getOptions();
        $isRequireOptions = false;

        if (!$options) {
            $this->dataSaver->updateValueIsRequire($product->getId(), (int)$isRequireOptions);
            return $product;
        }

        foreach ($options as $option) {
            if (!$option->getIsRequire()) {
                continue;
            }
            //prepare data
            if (is_null($option->getValues()) && is_array($option->getData('values'))) {
                $optionValues = [];
                foreach ($option->getData('values') as $valueDatum) {
                    $optionValues[] = $this->optionValueFactory->create()->setData($valueDatum);
                }
                $option->setValues($optionValues);
            }
            $optionRequireStatus = true;
            /* @var $validatorItem \MageWorx\OptionBase\Api\ValidatorInterface */
            foreach ($this->validationResolver->getValidators() as $key => $validatorItem) {
                if (!$validatorItem->canValidateCartCheckout($product, $option)) {
                    $optionRequireStatus = false;
                    break;
                }
            }

            if ($optionRequireStatus) {
                $isRequireOptions = true;
                break;
            }
        }

        $this->dataSaver->updateValueIsRequire($product->getId(), (int)$isRequireOptions);

        return $product;
    }
}