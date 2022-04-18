<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Observer;

use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\LazyLoad\Model\Output\LazyLoadProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;

class AjaxLazyLoadProcessor implements ObserverInterface
{
    /**
     * @var LazyLoadProcessor
     */
    private $lazyLoadProcessor;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LazyConfigFactory
     */
    private $lazyConfigFactory;

    public function __construct(
        ConfigProvider $configProvider,
        LazyConfigFactory $lazyConfigFactory,
        LazyLoadProcessor $lazyLoadProcessor
    ) {
        $this->lazyLoadProcessor = $lazyLoadProcessor;
        $this->configProvider = $configProvider;
        $this->lazyConfigFactory = $lazyConfigFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if (!$this->configProvider->isEnabled()) {
            return;
        }

        /** @var DataObject $data */
        if ($data = $observer->getData('data')) {
            if ($data->hasData('fullLazyConfig')) {
                $lazyConfig = $data->getData('fullLazyConfig');
            } else {
                $params = [];
                if ($data->hasData('pageType')) {
                    $params['pageType'] = $data->getData('pageType');
                }
                $lazyConfig = $this->lazyConfigFactory->create($params);
                if ($data->hasData('lazyConfig')) {
                    $newLazyConfig = array_merge_recursive($lazyConfig->getData(), $data->getData('lazyConfig'));
                    $lazyConfig->unsetData()->setData($newLazyConfig);
                }
            }
            $this->lazyLoadProcessor->setLazyConfig($lazyConfig);

            $page = $data->getData('page');
            $this->lazyLoadProcessor->processLazyImages($page);
            if ($lazyConfig->getData(LazyConfig::IS_LAZY)) {
                $page .= '<img ' . LazyLoadProcessor::LAZY_LOAD_PLACEHOLDER . ' onload="amlazy();this.remove();"/>';
            }
            $data->setData('page', $page);
        }
    }
}
