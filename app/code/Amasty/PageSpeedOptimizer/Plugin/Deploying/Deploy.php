<?php

namespace Amasty\PageSpeedOptimizer\Plugin\Deploying;

use Amasty\PageSpeedOptimizer\Model\Bundle\Bundle;
use Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\CollectionFactory;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedOptimizer\Model\OptionSource\BundlingType;
use Magento\Framework\Registry;

/**
 * Class Deploy collects files for bundling
 * previously saved in \Amasty\PageSpeedOptimizer\Controller\Bundle\Modules
 *
 * @see \Magento\Deploy\Service\Bundle
 */
class Deploy
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ConfigProvider $configProvider,
        CollectionFactory $collectionFactory,
        Registry $registry
    ) {
        $this->configProvider = $configProvider;
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
    }

    public function beforeDeploy($subject, $area, $theme, $locale)
    {
        if (!$this->configProvider->isEnabled()
            || $this->configProvider->getBundlingType() !== BundlingType::SUPER_BUNDLING
        ) {
            return null;
        }

        $result = false;
        if ($this->configProvider->isCloud()) {
            if ($files = $this->configProvider->getBundlingFiles()) {
                if (!empty($files[$area][$theme][$locale])) {
                    $result = $files[$area][$theme][$locale];
                }
            }
        } else {
            /** @var \Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('main_table.' . Bundle::AREA, $area);
            $collection->addFieldToFilter('main_table.' . Bundle::THEME, $theme);
            $collection->addFieldToFilter('main_table.' . Bundle::LOCALE, $locale);
            $collection->addFieldToSelect(Bundle::FILENAME);
            $result = $collection->getData();
            if (!empty($result)) {
                foreach ($result as &$item) {
                    $item = $item[Bundle::FILENAME];
                }
            }
        }

        $this->registry->register('am_bundle_files', $result);
    }

    public function afterDeploy()
    {
        $this->registry->unregister('am_bundle_files');
    }
}
