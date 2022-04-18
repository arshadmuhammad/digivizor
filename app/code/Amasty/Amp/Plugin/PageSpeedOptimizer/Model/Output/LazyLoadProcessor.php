<?php

declare(strict_types=1);

namespace Amasty\Amp\Plugin\PageSpeedOptimizer\Model\Output;

use Amasty\Amp\Model\ConfigProvider;
use \Magento\Framework\DataObject;

/**
 * Plugin to remove the PageSpeedOptimizer initialization script from all the AMP pages
 */
class LazyLoadProcessor
{
    const IS_LAZY = 'is_lazy';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function afterGetLazyConfig(
        \Amasty\PageSpeedOptimizer\Model\Output\LazyLoadProcessor $subject,
        DataObject $result
    ) : DataObject {
        if ($this->configProvider->isAmpPage() && $result->hasData(self::IS_LAZY)) {
            $result->unsetData(self::IS_LAZY);
        }

        return $result;
    }
}
