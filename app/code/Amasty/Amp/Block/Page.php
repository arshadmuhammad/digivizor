<?php

namespace Amasty\Amp\Block;

use Amasty\Amp\Model\ConfigProvider;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Generator\Head;
use Magento\Theme\Model\Favicon\Favicon;

class Page extends \Magento\Framework\View\Element\Template
{
    const BASE_DIR = '{baseDir}';

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var \Magento\Theme\Model\Favicon\Favicon
     */
    private $favicon;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        DriverInterface $driver,
        ConfigProvider $configProvider,
        Reader $moduleReader,
        Favicon $favicon,
        Config $pageConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->driver = $driver;
        $this->moduleReader = $moduleReader;
        $this->favicon = $favicon;
        $this->assetRepo = $context->getAssetRepository();
        $this->pageConfig = $pageConfig;
        $this->configProvider = $configProvider;
    }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getFileContent($file)
    {
        try {
            $viewDir = $this->moduleReader->getModuleDir(Dir::MODULE_VIEW_DIR, 'Amasty_Amp');
            $content = $this->driver->fileGetContents($viewDir . '/' . $file . '.min.css');
            $url = $this->getBaseStaticUrl();
            $content = str_replace(self::BASE_DIR, $url, $content);
        } catch (\Exception $e) {
            $content = '';
        }

        return $content;
    }

    private function getBaseStaticUrl(): string
    {
        $baseUrl = $this->assetRepo->getStaticViewFileContext()->getBaseUrl();
        $url = preg_replace('@(pub\/)?static\/(version\d+\/)?@', '', $baseUrl);

        if (!$this->configProvider->isPubRootDirectory()) {
            $url = sprintf('%s/pub', rtrim($url, '/'));
        }

        return rtrim($url, '/');
    }

    /**
     * @return string
     */
    public function getFaviconUrl()
    {
        $remoteFavicon = $this->favicon->getFaviconFile();
        $asset = $remoteFavicon
            ? $this->assetRepo->createRemoteAsset($remoteFavicon, Head::VIRTUAL_CONTENT_TYPE_LINK)
            : $this->assetRepo->createAsset($this->favicon->getDefaultFavicon());

        return $asset->getUrl();
    }

    /**
     * @return string
     */
    public function getBodyClass()
    {
        $attributes = $this->pageConfig->getElementAttributes('body');

        return $attributes['class'] ?? '';
    }
}
