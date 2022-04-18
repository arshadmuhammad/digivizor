<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\ViewModel;

use Amasty\CheckoutCore\Model\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class OnepageDataProvider implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $configProvider;

    public function __construct(Config $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->configProvider->getTitle();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->configProvider->getDescription();
    }
}
