<?php

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Output;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\ObjectManagerInterface;

class ImageReplaceProcessor implements OutputProcessorInterface
{
    const IMAGE_REGEXP = '<img([^>]*?)src=(\"|\'|)(.*?)(\"|\'| )(.*?)>';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    /**
     * @var ReplaceConfig\ReplaceConfigFactory
     */
    private $replaceConfigFactory;

    /**
     * @var LazyConfig\LazyConfig|DataObject
     */
    private $lazyConfig;

    /**
     * @var ReplaceConfig\ReplaceConfig
     */
    private $replaceConfig;

    public function __construct(
        ConfigProvider $configProvider,
        DataObjectFactory $dataObjectFactory,
        ObjectManagerInterface $objectManager,
        ReplacerCompositeInterface $imageReplacer,
        ReplaceConfig\ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->configProvider = $configProvider;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->objectManager = $objectManager;
        $this->imageReplacer = $imageReplacer;
        $this->replaceConfigFactory = $replaceConfigFactory;
    }

    public function process(string &$output): bool
    {
        if ($this->getLazyConfig() !== null && $this->getLazyConfig()->getData('is_lazy')) {
            return true;
        }

        if ($this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::IS_REPLACE_IMAGES)) {
            $tempOutput = preg_replace('/<script.*?>.*?<\/script.*?>/is', '', $output);

            if (preg_match_all('/' . static::IMAGE_REGEXP . '/is', $tempOutput, $images)) {
                foreach ($images[0] as $key => $image) {
                    if ($this->skipIfContain(
                        $image,
                        $this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::REPLACE_IMAGES_IGNORE_LIST)
                    )) {
                        continue;
                    }

                    if ($this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::IS_REPLACE_WITH_USER_AGENT)) {
                        $algorithm = ReplacerCompositeInterface::REPLACE_BEST;
                    } else {
                        $algorithm = ReplacerCompositeInterface::REPLACE_PICTURE;
                    }

                    $newImg = $this->imageReplacer->replace($algorithm, $image, $images[3][$key]);
                    $output = str_replace($image, $newImg, $output);
                }
            }
        }

        return true;
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

    public function getLazyConfig(): DataObject
    {
        if ($this->lazyConfig === null) {
            try {
                $this->lazyConfig = $this->objectManager->get(LazyConfig\LazyConfig::class);
            } catch (\Throwable $e) {
                $this->lazyConfig = $this->dataObjectFactory->create();
            }
        }

        return $this->lazyConfig;
    }

    public function getReplaceConfig(): DataObject
    {
        if ($this->replaceConfig === null) {
            $this->replaceConfig = $this->replaceConfigFactory->create();
        }

        return $this->replaceConfig;
    }
}
