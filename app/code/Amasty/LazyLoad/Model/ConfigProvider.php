<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model;

use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    protected $pathPrefix = 'amoptimizer/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const LAZY_LOAD = 'lazy_load_general/lazy_load';
    const LAZY_LOAD_SCRIPT = 'lazy_load_general/lazy_load_script';
    const PRELOAD_IMAGES = 'lazy_load_general/preload_images';
    const SKIP_IMAGES_COUNT = 'lazy_load_general/skip_images_count';
    const REPLACE_IGNORE_IMAGES = 'lazy_load_general/webp_resolutions_ignore';
    const IGNORE_IMAGES = 'lazy_load_general/ignore_list';
    const RESOLUTIONS = 'lazy_load_general/resolutions';
    const IMAGE_OPTIMIZATION_TYPE = 'lazy_load_general/image_optimization_type';
    const REPLACE_IMAGES_USING_USER_AGENT = 'images/replace_images_using_user_agent';
    const REPLACE_IMAGES_USING_USER_AGENT_IGNORE_LIST = 'images/replace_images_using_user_agent_ignore_list';
    const SKIP_STRATEGY = 'lazy_load_general/preload_images_strategy';
    /**#@-*/

    const PART_IS_LAZY = '/lazy_load';
    const PART_SCRIPT = '/lazy_load_script';
    const PART_STRATEGY = '/preload_images_strategy';
    const PART_PRELOAD = '/preload_images';
    const PART_SKIP = '/skip_images_count';
    const PART_IGNORE = '/ignore_list';
    const PART_REPLACE_WITH_WEBP = '/webp_resolutions';
    const PART_REPLACE_IGNORE = '/webp_resolutions_ignore';
    const PART_ENABLE_CUSTOM_LAZY = '/enable_custom_lazyload';

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('amlazyload/general/enabled', ScopeInterface::SCOPE_STORE);
    }

    public function isLazyLoad(): bool
    {
        return $this->isSetFlag(self::LAZY_LOAD);
    }

    public function lazyLoadScript(): string
    {
        return (string)$this->getValue(self::LAZY_LOAD_SCRIPT);
    }

    public function isPreloadImages(): bool
    {
        return $this->isSetFlag(self::PRELOAD_IMAGES);
    }

    public function skipImagesCount(string $type = ''): int
    {
        return (int)$this->getValue(self::SKIP_IMAGES_COUNT . $type);
    }

    public function getIgnoreImages(): array
    {
        return $this->convertStringToArray($this->getValue(self::IGNORE_IMAGES));
    }

    public function getResolutions(): array
    {
        if ($this->getValue(self::RESOLUTIONS) !== '') {
            return explode(',', $this->getValue(self::RESOLUTIONS));
        }

        return [];
    }

    public function isReplaceImagesUsingUserAgent(): bool
    {
        return $this->isSetFlag(self::REPLACE_IMAGES_USING_USER_AGENT);
    }

    public function getReplaceImagesUsingUserAgentIgnoreList(): array
    {
        return $this->convertStringToArray($this->getValue(self::REPLACE_IMAGES_USING_USER_AGENT_IGNORE_LIST));
    }

    public function getSkipStrategy(): int
    {
        return (int)$this->getValue(self::SKIP_STRATEGY);
    }

    public function getConfig($path)
    {
        return $this->getValue($path);
    }

    public function getCustomValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function convertStringToArray(?string $data, string $separator = PHP_EOL): array
    {
        if (empty($data)) {
            return [];
        }

        return array_filter(array_map('trim', explode($separator, $data)));
    }
}
