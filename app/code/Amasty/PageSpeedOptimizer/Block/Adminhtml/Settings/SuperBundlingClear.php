<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\CollectionFactory;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SuperBundlingClear extends Field
{
    /**
     * @var \Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Collection
     */
    private $collection;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collection = $collectionFactory->create();
        $this->configProvider = $configProvider;
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setData('value', __("Clear"));
        $element->setData('class', "amoptimizer-button -clear");
        $element->setData('onclick', "location.href = '" . $this->getActionUrl() . "'");

        if ($this->configProvider->getBundleStep() || !$this->collection->getSize()) {
            $element->setData('readonly', true);

            return parent::_getElementHtml($element);
        }

        return parent::_getElementHtml($element)
            . '<div style="margin-top:10px">'
            . __('The JS optimization is finished. Please check your website.')
            . '<br>' . __('Use Clear Bundle button to roll back the JavaScript optimization.')
            . '</div>';
    }

    public function getActionUrl(): string
    {
        return $this->_urlBuilder->getUrl('amoptimizer/bundle/clear');
    }
}
