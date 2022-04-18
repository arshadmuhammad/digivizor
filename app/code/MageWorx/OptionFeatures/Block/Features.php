<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Block;

use Magento\Framework\Registry;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Helper\System as SystemHelper;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionFeatures\Model\Config\Features as FeaturesConfig;

class Features extends Template
{
    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var SystemHelper
     */
    protected $systemHelper;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FeaturesConfig
     */
    protected $featuresConfig;

    /**
     * @var array
     */
    protected $selectionLimitCache = [];

    /**
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Helper $helper
     * @param SystemHelper $systemHelper
     * @param BaseHelper $baseHelper
     * @param Registry $registry
     * @param FeaturesConfig $featuresConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Helper $helper,
        SystemHelper $systemHelper,
        BaseHelper $baseHelper,
        Registry $registry,
        FeaturesConfig $featuresConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->jsonEncoder    = $jsonEncoder;
        $this->helper         = $helper;
        $this->systemHelper   = $systemHelper;
        $this->baseHelper     = $baseHelper;
        $this->registry       = $registry;
        $this->featuresConfig = $featuresConfig;
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        $data = [
            'question_image'                        => $this->getViewFileUrl(
                'MageWorx_OptionFeatures::image/question.png'
            ),
            'value_description_enabled'             => $this->helper->isValueDescriptionEnabled(),
            'option_description_enabled'            => $this->helper->isOptionDescriptionEnabled(),
            'option_description_mode'               => $this->helper->getOptionDescriptionMode(),
            'option_description_modes'              => [
                'disabled' => Helper::OPTION_DESCRIPTION_DISABLED,
                'tooltip'  => Helper::OPTION_DESCRIPTION_TOOLTIP,
                'text'     => Helper::OPTION_DESCRIPTION_TEXT,
            ],
            'product_price_display_mode'            => $this->helper->getProductPriceDisplayMode(),
            'additional_product_price_display_mode' => $this->helper->getAdditionalProductPriceFieldMode()
        ];

        $storeId = $this->getProduct() ? $this->getProduct()->getStoreId() : 0;
        $data['shareable_link_hint_text'] = $this->helper->getShareableLinkHintText($storeId);

        return $this->jsonEncoder->encode($data);
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
    public function getIsDefaultJsonData()
    {
        $data = [
            'is_default_values' => $this->featuresConfig->getIsDefaultArray($this->registry->registry('product'))
        ];

        return $this->jsonEncoder->encode($data);
    }

    /**
     * @return string
     */
    public function getSelectionLimitJsonData()
    {
        $data = [];

        $product = $this->getProduct();
        if (!$product) {
            return $this->jsonEncoder->encode($data);
        }

        if (!empty($this->selectionLimitCache[$product->getId()])) {
            return $this->selectionLimitCache[$product->getId()];
        }

        $options = $product->getOptions();
        foreach ($options as $option) {
            $data[$option->getOptionId()] = [
                'selection_limit_from' => $option->getSelectionLimitFrom(),
                'selection_limit_to'   => $option->getSelectionLimitTo()
            ];
        }

        return $this->selectionLimitCache[$product->getId()] = $this->jsonEncoder->encode($data);
    }
}
