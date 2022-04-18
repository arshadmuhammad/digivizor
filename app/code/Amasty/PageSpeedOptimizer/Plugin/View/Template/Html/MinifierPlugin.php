<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Plugin\View\Template\Html;

use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Code\Minifier\Adapter\Js\JShrink;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Template\Html\Minifier;

class MinifierPlugin
{
    /**
     * @var JShrink
     */
    private $JShrink;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        JShrink $JShrink,
        Filesystem $filesystem,
        ConfigProvider $configProvider
    ) {
        $this->JShrink = $JShrink;
        $this->filesystem = $filesystem;
        $this->configProvider = $configProvider;
    }

    public function afterMinify(Minifier $subject, $minificationResult, $file)
    {
        if (!$this->configProvider->isMifiniedJs() || !$this->configProvider->isMinifiedJsInPhtml()) {
            return $minificationResult;
        }

        $htmlDirectoryWrite = $this->filesystem->getDirectoryWrite(DirectoryList::TMP_MATERIALIZATION_DIR);
        $fileRelativePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getRelativePath($file);
        if (in_array($fileRelativePath, $this->configProvider->getMinifyJsPhtmlBlacklist())) {
            return $minificationResult;
        }

        $minifiedFileContent = preg_replace_callback(
            '/(?<!<<)<script[^>]*>(?>.*?<\/script>)/is',
            function ($scriptContent) {
                return $this->minifyJsContent($scriptContent[0]);
            },
            $htmlDirectoryWrite->readFile($fileRelativePath)
        );

        return $htmlDirectoryWrite->writeFile($fileRelativePath, rtrim($minifiedFileContent));
    }

    private function minifyJsContent(string $inlineJs):  string
    {
        //Storing Heredocs and PHP scripts parts
        $heredocs = $phpParts = [];
        $inlineJs = preg_replace_callback(
            '/<<<([A-z]+).*?\1;/ims',
            function ($match) use (&$heredocs) {
                $heredocs[] = $match[0];

                return '__MINIFIED_HEREDOC__' . (count($heredocs) - 1);
            },
            $inlineJs
        );
        $inlineJs = preg_replace_callback(
            '/\<\?(?=(=|php)).*?\?\>/ms',
            function ($match) use (&$phpParts) {
                $phpParts[] = $match[0];

                return '__MINIFIED_PHP__' . (count($phpParts) - 1);
            },
            $inlineJs
        );

        try {
            $inlineJs = $this->JShrink->minify($inlineJs);
        } catch (\Exception $e) {
            null; // Do nothing and chill
        }

        //Restoring Heredocs and PHP scripts parts
        $inlineJs = preg_replace_callback(
            '/__MINIFIED_HEREDOC__(\d+)/ims',
            function ($match) use ($heredocs) {
                return $heredocs[(int)$match[1]];
            },
            $inlineJs
        );
        $inlineJs = preg_replace_callback(
            '/__MINIFIED_PHP__(\d+)/ims',
            function ($match) use ($phpParts) {
                return $phpParts[(int)$match[1]];
            },
            $inlineJs
        );

        return $inlineJs;
    }
}
