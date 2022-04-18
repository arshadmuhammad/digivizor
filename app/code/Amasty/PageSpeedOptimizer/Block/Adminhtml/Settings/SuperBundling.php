<?php
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\CollectionFactory;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Url;

class SuperBundling extends Field
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        Url $url,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
        $this->url = $url;
        $this->collectionFactory = $collectionFactory;
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setData('value', __("Run"));
        $element->setData('class', "amoptimizer-button");

        $block = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('Amasty_PageSpeedOptimizer::super_bundling_button.phtml')
            ->setStartUrl($this->getStartUrl())
            ->setFinishUrl($this->getFinishUrl());

        return parent::_getElementHtml($element) . $block->toHtml();
    }

    public function getStartUrl(): string
    {
        return $this->_urlBuilder->getUrl('amoptimizer/bundle/start');
    }

    public function getFinishUrl(): string
    {
        return $this->_urlBuilder->getUrl('amoptimizer/bundle/finish');
    }
}
