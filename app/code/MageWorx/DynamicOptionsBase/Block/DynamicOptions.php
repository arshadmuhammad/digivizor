<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DynamicOptionsBase\Block;

use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class DynamicOptions extends Template
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var array
     */
    private $validationCache = [];

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * DynamicOptions constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param Json $jsonSerializer
     * @param Registry $registry
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context,
        Json $jsonSerializer,
        Registry $registry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Helper\Data $catalogData,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->storeManager   = $storeManager;
        $this->jsonSerializer = $jsonSerializer;
        $this->registry       = $registry;
        $this->moduleManager  = $moduleManager;
        $this->catalogData    = $catalogData;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    protected function getProduct()
    {
        $product = $this->registry->registry('product');
        if (!$product || !$product->getId()) {
            return null;
        }

        return $product;
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        $data = [];

        $product = $this->getProduct();
        if (!$product) {
            return $this->jsonSerializer->serialize($data);
        }

        if (!empty($this->validationCache[$product->getId()])) {
            return $this->validationCache[$product->getId()];
        }

        $options = $product->getMageworxDynamicOptions();
        $data['options_data'] = [];
        foreach ($options as $option) {
            $data['options_data'][$option->getOptionId()] = $option->getData();
        }

        if ($product->getPricePerUnit()) {
            $pricePerUnit = $this->convertPricePerUnit((string)$product->getPricePerUnit());

            $data['prices'] = [
                'oldPrice' => [
                    'amount' => $pricePerUnit
                ],
                'basePrice' => [
                    'amount' => $this->catalogData->getTaxPrice($product, $pricePerUnit, false),
                ],
                'finalPrice' => [
                    'amount' => $this->catalogData->getTaxPrice($product, $pricePerUnit, true),
                ],
            ];

        } else {

            $data['prices'] = [
                'oldPrice'   => ['amount' => 0],
                'basePrice'  => ['amount' => 0],
                'finalPrice' => ['amount' => 0],
            ];
        }

        return $this->validationCache[$product->getId()] = $this->jsonSerializer->serialize($data);
    }

    /**
     * @param string $pricePerUnit
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function convertPricePerUnit(string $pricePerUnit): float
    {
        return $this->storeManager->getStore()->getCurrentCurrencyRate() * $pricePerUnit;
    }
}
