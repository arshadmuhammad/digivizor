<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\Output;

use Amasty\LazyLoad\Model\Asset\Collector\PreloadImageCollector;
use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\LazyScript\LazyScriptProvider;
use Amasty\LazyLoad\Model\OptionSource\PreloadStrategy;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\View\Asset\Repository;

class LazyLoadProcessor implements OutputProcessorInterface
{
    const IMAGE_REGEXP = '<img([^>]*?)src=(\"|\'|)(.*?)(\"|\'| )(.*?)>';
    const LAZY_LOAD_PLACEHOLDER = 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABC'
        . 'AQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII="';

    /**
     * @var string
     */
    public $pageType;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var DataObjectFactory
     */
    private $lazyConfigFactory;

    /**
     * @var DataObject
     */
    private $lazyConfig;

    /**
     * @var LazyScriptProvider
     */
    private $lazyScriptProvider;

    /**
     * @var PreloadImageCollector
     */
    private $preloadImageCollector;

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    public function __construct(
        ConfigProvider $configProvider,
        Repository $assetRepo,
        LazyScriptProvider $lazyScriptProvider,
        LazyConfigFactory $lazyConfigFactory,
        PreloadImageCollector $preloadImageCollector,
        ReplacerCompositeInterface $imageReplacer
    ) {
        $this->configProvider = $configProvider;
        $this->assetRepo = $assetRepo;
        $this->lazyScriptProvider = $lazyScriptProvider;
        $this->lazyConfigFactory = $lazyConfigFactory;
        $this->preloadImageCollector = $preloadImageCollector;
        $this->imageReplacer = $imageReplacer;
    }

    public function process(string &$output): bool
    {
        if ($this->configProvider->isEnabled() && $this->getLazyConfig()->getData(LazyConfig::IS_LAZY)) {
            $this->processLazyImages($output);

            if ($this->getLazyConfig()->hasData(LazyConfig::LAZY_SCRIPT)) {
                $this->addLazyScript($output, $this->getLazyConfig()->getData(LazyConfig::LAZY_SCRIPT));
            }
        }

        return true;
    }

    public function processLazyImages(&$output)
    {
        $tempOutput = preg_replace('/<script[^>]*>(?>.*?<\/script>)/is', '', $output);

        if (preg_match_all('/' . self::IMAGE_REGEXP . '/is', $tempOutput, $images)) {
            $skipCounter = 1;
            $preloadStrategy = $this->getLazyConfig()->getData(LazyConfig::LAZY_PRELOAD_STRATEGY);
            $userAgentIgnoreList = $this->getLazyConfig()->getData(LazyConfig::USER_AGENT_IGNORE_LIST);

            foreach ($images[0] as $key => $image) {
                $newImg = $image;

                if ($skipCounter <= $this->getLazyConfig()->getData(LazyConfig::LAZY_SKIP_IMAGES)) {
                    if ($this->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)) {
                        if (!$this->skipIfContain($image, $userAgentIgnoreList)) {
                            $newImg = $this->imageReplacer->replace(
                                ReplacerCompositeInterface::REPLACE_BEST,
                                $image,
                                $images[3][$key]
                            );
                            $output = str_replace($image, $newImg, $output);
                            $this->preloadImageCollector->addImageAsset($newImg);
                        }
                    } else {
                        if ($preloadStrategy == PreloadStrategy::SKIP_IMAGES) {
                            $this->preloadImageCollector->addImageAsset($image);
                            $skipCounter++;
                            continue;
                        }

                        $newImg = $this->imageReplacer->replace(
                            ReplacerCompositeInterface::REPLACE_PICTURE,
                            $image,
                            $images[3][$key]
                        );
                        $output = str_replace($image, $newImg, $output);
                    }

                    $skipCounter++;
                    continue;
                }

                if (!$this->skipIfContain($image, $this->getLazyConfig()->getData(LazyConfig::LAZY_IGNORE_LIST))) {
                    $replace = 'src=' . $images[2][$key] . $images[3][$key] . $images[4][$key];
                    $newImg = str_replace($replace, self::LAZY_LOAD_PLACEHOLDER . ' data-am' . $replace, $image);
                }

                if ($this->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)
                    && !$this->skipIfContain($image, $userAgentIgnoreList)
                ) {
                    $newImg = $this->imageReplacer->replace(
                        ReplacerCompositeInterface::REPLACE_BEST,
                        $newImg,
                        $images[3][$key]
                    );
                }

                $newImg = preg_replace('/srcset=[\"\'\s]+(.*?)[\"\']+/is', '', $newImg);
                $output = str_replace($image, $newImg, $output);
            }
        }
    }

    public function addLazyScript(&$output, $lazyScriptType)
    {
        $lazy = '<script>window.amlazy = function() {'
            . 'if (typeof window.amlazycallback !== "undefined") {'
            . 'setTimeout(window.amlazycallback, 500);setTimeout(window.amlazycallback, 1500);}'
            . '}</script>';
        if ($lazyScript = $this->lazyScriptProvider->get($lazyScriptType)) {
            $lazy .= $lazyScript->getCode();
        }

        $output = str_ireplace('</body', $lazy . '</body', $output);
    }

    private function skipIfContain(string $searchString, array $list): bool
    {
        $skip = false;
        foreach ($list as $item) {
            if (strpos($searchString, $item) !== false) {
                $skip = true;
                break;
            }
        }

        return $skip;
    }

    public function setLazyConfig(DataObject $lazyConfig): self
    {
        $this->lazyConfig = $lazyConfig;

        return $this;
    }

    public function getLazyConfig(): DataObject
    {
        if ($this->lazyConfig === null) {
            $this->lazyConfig = $this->lazyConfigFactory->create();
        }

        return $this->lazyConfig;
    }
}
