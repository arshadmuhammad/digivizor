<?php

namespace Amasty\PageSpeedOptimizer\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;

class MoveFont
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var \Amasty\PageSpeedOptimizer\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * Storage fpr already processed files to avoid overwriting
     *
     * @var array
     */
    private $processedFiles = [];

    public function __construct(
        \Amasty\PageSpeedOptimizer\Model\ConfigProvider $configProvider,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->configProvider = $configProvider;
        $this->filesystem = $filesystem;
    }

    /**
     * @param \Magento\Framework\View\Asset\MergeStrategy\Direct $subject
     * @param \Magento\Framework\View\Asset\MergeableInterface[] $assetsToMerge
     * @param \Magento\Framework\View\Asset\LocalInterface $resultAsset
     *
     * @see \Magento\Framework\View\Asset\MergeStrategy\Direct::merge
     */
    public function beforeMerge($subject, $assetsToMerge, $resultAsset)
    {
        if ($this->configProvider->isEnabled()
            && $this->configProvider->isMoveFont() && $resultAsset->getContentType() === 'css'
        ) {
            $this->filePath = $resultAsset->getPath();
        }
    }

    /**
     * @param \Magento\Framework\View\Asset\MergeStrategy\Direct $subject
     *
     * @see \Magento\Framework\View\Asset\MergeStrategy\Direct::merge
     */
    public function afterMerge($subject)
    {
        if ($this->configProvider->isEnabled()
            && $this->configProvider->isMoveFont()
            && $this->filePath
            && !in_array($this->filePath, $this->processedFiles)
        ) {
            $staticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
            $basename = $this->basename($this->filePath);
            $origFileName = 'orig_' . $basename;
            $newPath = str_replace($basename, $origFileName, $this->filePath);
            $staticDir->copyFile($this->filePath, $newPath);
            $mergedContent = $staticDir->readFile($this->filePath);
            $fonts = [];
            $fontIgnoreList = $this->configProvider->getFontIgnoreList();
            $mergedContent = preg_replace_callback(
                '/@font-face\s*\{.*?\}/is',
                function ($match) use (&$fonts, $fontIgnoreList) {
                    foreach ($fontIgnoreList as $ignoreFont) {
                        if (strpos($match[0], $ignoreFont) !== false) {
                            return $match[0];
                        }
                    }
                    $fonts[] = $match[0];
                    return '';
                },
                $mergedContent
            );
            if (!empty($fonts)) {
                $fontsPath = str_replace(
                    $this->basename($this->filePath),
                    'fonts_' . $this->basename($this->filePath),
                    $this->filePath
                );
                $staticDir->writeFile($fontsPath, implode('', $fonts));
                $staticDir->writeFile($this->filePath, $mergedContent);
            }
            $this->processedFiles[] = $this->filePath;
        }
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function basename($file)
    {
        //phpcs:ignore
        return basename($file);
    }
}
