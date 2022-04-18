<?php

namespace Amasty\Amp\Model;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Data\CollectionDataSourceInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract implements CollectionDataSourceInterface
{
    const GENERAL_ENABLED_ON_PRODUCT = 'general/enabled';
    const GENERAL_ENABLED_ON_CATEGORY = 'general/enabled_on_category';
    const GENERAL_ENABLED_ON_CMS = 'general/enabled_on_cms';
    const GENERAL_ENABLED_ON_HOME = 'general/enabled_on_home';
    const GENERAL_REDIRECT_MOBILE = 'general/redirect_mobile';
    const AMP_DESIGN_LINK_COLOR = 'amp/design/link_color';
    const AMP_DESIGN_LINK_COLOR_FOCUS = 'amp/design/link_color_focus';
    const AMP_DESIGN_BUTTON_BACKGROUND_COLOR = 'amp/design/button_background_color';
    const AMP_DESIGN_BUTTON_BACKGROUND_COLOR_FOCUS = 'amp/design/button_background_color_focus';
    const AMP_DESIGN_BUTTON_TEXT_COLOR = 'amp/design/button_text_color';
    const AMP_DESIGN_BUTTON_TEXT_COLOR_FOCUS = 'amp/design/button_text_color_focus';
    const AMP_DESIGN_FOOTER_BACKGROUND = 'amp/design/footer_background';
    const AMP_DESIGN_HEADER_BACKGROUND = 'amp/design/header_background';
    const AMP_LOGO_IMAGE_WIDTH = 'amp/logo/image_width';
    const AMP_LOGO_IMAGE_HEIGHT = 'amp/logo/image_height';
    const AMP_LOGO_IMAGE_ALIGNMENT = 'amp/logo/logo_alignment';
    const AMP_CATEGORY_IMAGE_WIDTH = 'amp/category_image/image_width';
    const AMP_CATEGORY_IMAGE_HEIGHT = 'amp/category_image/image_height';
    const CATALOG_PRODUCT_VIEW = 'catalog_product_view';
    const CATALOG_CATEGORY_VIEW = 'catalog_category_view';
    const CMS_PAGE_VIEW = 'cms_page_view';
    const CMS_INDEX_INDEX = 'cms_index_index';
    const EMPTY_JSON = '""';
    const TAX_DISPLAY_TYPE = 'tax/display/type';
    const DIRECTORY_ROOT_IS_PUB = 'directories/document_root_is_pub';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_amp/';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        Registry $registry,
        DeploymentConfig $deploymentConfig
    ) {
        parent::__construct($scopeConfig);
        $this->request = $request;
        $this->registry = $registry;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @param $actionName
     * @return bool
     */
    public function isAmpEnabledOnCurrentPage($actionName = null)
    {
        $actionName = $actionName ?: $this->request->getFullActionName();
        switch ($actionName) {
            case self::CATALOG_PRODUCT_VIEW:
                $isEnabled = $this->isAmpProductPage();
                break;
            case self::CATALOG_CATEGORY_VIEW:
                $isEnabled = $this->isAmpCategoryPage();
                break;
            case self::CMS_PAGE_VIEW:
                $isEnabled = $this->isAmpCmsPage();
                break;
            case self::CMS_INDEX_INDEX:
                $isEnabled = $this->isAmpHomePage();
                break;
            default:
                $isEnabled = false;
                break;
        }

        return $isEnabled;
    }

    /**
     * @return bool
     */
    public function isProductEnabled()
    {
        return (bool)$this->getModuleConfig(self::GENERAL_ENABLED_ON_PRODUCT);
    }

    /**
     * @return bool
     */
    public function isCategoryEnabled()
    {
        return (bool)$this->getModuleConfig(self::GENERAL_ENABLED_ON_CATEGORY);
    }

    /**
     * @return bool
     */
    public function isCmsEnabled()
    {
        return (bool)$this->getModuleConfig(self::GENERAL_ENABLED_ON_CMS);
    }

    /**
     * @return bool
     */
    public function isHomeEnabled()
    {
        return (bool)$this->getModuleConfig(self::GENERAL_ENABLED_ON_HOME);
    }

    /**
     * @return bool
     */
    public function isAmpProductPage()
    {
        return $this->isProductEnabled() && $this->isAmpUrl();
    }

    /**
     * @return bool
     */
    public function isAmpCategoryPage()
    {
        return $this->isCategoryEnabled() && $this->isAmpUrl();
    }

    /**
     * @return bool
     */
    public function isAmpCmsPage()
    {
        return $this->isCmsEnabled() && $this->isAmpUrl();
    }

    /**
     * @return bool
     */
    public function isAmpHomePage()
    {
        return $this->isHomeEnabled() && $this->isAmpUrl();
    }

    /**
     * @return bool
     */
    public function isAmpPage()
    {
        return $this->isAmpProductPage()
            || $this->isAmpCategoryPage()
            || $this->isAmpCmsPage()
            || $this->isAmpHomePage();
    }

    /**
     * @return bool
     */
    public function isAmpUrl()
    {
        return strpos($this->request->getOriginalPathInfo(), '/' . UrlConfigProvider::AMP . '/') !== false
            || $this->request->getParam('amp_page');
    }

    /**
     * @param Category|null $category
     *
     * @return bool
     */
    public function isValidCategory($category = null)
    {
        $category = $category ?: $this->registry->registry('current_category');

        return $category && $category->getIsAnchor() && $category->getDisplayMode() !== Category::DM_PAGE;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'amasty_amp/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getLinkColor()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_LINK_COLOR);
    }

    /**
     * @return string
     */
    public function getLinkColorFocus()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_LINK_COLOR_FOCUS);
    }

    /**
     * @return string
     */
    public function getButtonBackground()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_BUTTON_BACKGROUND_COLOR);
    }

    /**
     * @return string
     */
    public function getButtonBackgroundFocus()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_BUTTON_BACKGROUND_COLOR_FOCUS);
    }

    /**
     * @return string
     */
    public function getButtonTextColor()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_BUTTON_TEXT_COLOR);
    }

    /**
     * @return string
     */
    public function getButtonTextColorFocus()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_BUTTON_TEXT_COLOR_FOCUS);
    }

    /**
     * @return string
     */
    public function getFooterBackground()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_FOOTER_BACKGROUND);
    }

    /**
     * @return string
     */
    public function getHeaderBackground()
    {
        return $this->getModuleConfig(self::AMP_DESIGN_HEADER_BACKGROUND);
    }

    /**
     * @return int
     */
    public function getLogoWidth()
    {
        return (int)$this->getModuleConfig(self::AMP_LOGO_IMAGE_WIDTH);
    }

    /**
     * @return int
     */
    public function getLogoHeight()
    {
        return (int)$this->getModuleConfig(self::AMP_LOGO_IMAGE_HEIGHT);
    }

    /**
     * @return string
     */
    public function getLogoAlignment()
    {
        return $this->getModuleConfig(self::AMP_LOGO_IMAGE_ALIGNMENT);
    }

    /**
     * @return int
     */
    public function getCategoryImageWidth()
    {
        return (int)$this->getModuleConfig(self::AMP_CATEGORY_IMAGE_WIDTH);
    }

    /**
     * @return int
     */
    public function getCategoryImageHeight()
    {
        return (int)$this->getModuleConfig(self::AMP_CATEGORY_IMAGE_HEIGHT);
    }

    /**
     * @param $message
     * @return string
     */
    public function getMessageType($message)
    {
        $type = 'default';
        if (is_object($message)) {
            switch (get_class($message)) {
                case \Magento\Framework\Message\Success::class:
                    $type = 'success';
                    break;
                case \Magento\Framework\Message\Error::class:
                    $type = 'error';
                    break;
                case \Magento\Framework\Message\Notice::class:
                    $type = 'notice';
                    break;
            }
        }

        return $type;
    }

    /**
     * @return bool
     */
    public function isRedirectMobile()
    {
        return (bool)$this->getModuleConfig(self::GENERAL_REDIRECT_MOBILE);
    }

    /**
     * @return int
     */
    public function getTaxDisplayType()
    {
        return (int) $this->scopeConfig->getValue(self::TAX_DISPLAY_TYPE);
    }

    public function isPubRootDirectory(): bool
    {
        $isDirectoryRootPub = $this->deploymentConfig->get(self::DIRECTORY_ROOT_IS_PUB);

        return $isDirectoryRootPub === null || (bool) $isDirectoryRootPub;
    }
}
