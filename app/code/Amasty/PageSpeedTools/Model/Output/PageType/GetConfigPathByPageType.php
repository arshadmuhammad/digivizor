<?php

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Output\PageType;

use Magento\Framework\View\Layout;

class GetConfigPathByPageType
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var array
     */
    private $configPathsByPageType;

    private $pageTypeConfigPath;

    /**
     * @var string
     */
    private $defaultConfigPath;

    public function __construct(
        string $defaultConfigPath,
        array $configPathsByPageType,
        Layout $layout
    ) {
        $this->layout = $layout;
        $this->configPathsByPageType = $configPathsByPageType;
        $this->defaultConfigPath = $defaultConfigPath;
    }

    public function execute(string $pageType = ''): string
    {
        if ($pageType) {
            return $this->configPathsByPageType[$pageType] ?? $this->defaultConfigPath;
        }

        if ($this->pageTypeConfigPath !== null) {
            return $this->pageTypeConfigPath;
        }

        $handles = $this->getPageHandles();
        foreach ($this->configPathsByPageType as $handle => $configPath) {
            if (in_array($handle, $handles)) {
                $this->pageTypeConfigPath = $configPath;
                break;
            }
        }
        if ($this->pageTypeConfigPath === null) {
            $this->pageTypeConfigPath = $this->defaultConfigPath;
        }

        return $this->pageTypeConfigPath;
    }

    private function getPageHandles(): array
    {
        return $this->layout->getUpdate()->getHandles();
    }
}
