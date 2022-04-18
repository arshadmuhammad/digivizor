<?php

namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;

class MoveCssProcessor implements OutputProcessorInterface
{
    /**
     * @var \Amasty\PageSpeedOptimizer\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Amasty\PageSpeedTools\Model\DeviceDetect
     */
    private $deviceDetector;

    public function __construct(
        \Amasty\PageSpeedOptimizer\Model\ConfigProvider $configProvider,
        \Amasty\PageSpeedTools\Model\DeviceDetect $deviceDetector
    ) {
        $this->configProvider = $configProvider;
        $this->deviceDetector = $deviceDetector;
    }

    public function process(string &$output): bool
    {
        $moveStyles = '';
        if ($this->configProvider->isMovePrintCss()) {
            $output = preg_replace_callback(
                '/\<link[^>]*media\s*=\s*["\']+print["\']+[^>]*\>/si',
                function ($print) use (&$moveStyles) {
                    $moveStyles .= $print[0];
                    return '';
                },
                $output
            );
        }

        if ($this->configProvider->isMoveFont()
            && preg_match('/<link[^>]*href\s*=\s*["\']+([^"\']*merged[^"\']*)["\']+[^>]*\>/is', $output, $m)
        ) {
            if ($this->isMoveForCurrentDevice()) {
                $fontLink = str_replace(
                    $this->basename($m[1]),
                    'fonts_' . $this->basename($m[1]),
                    $m[1]
                );
                $moveStyles .= '<link rel="stylesheet"  type="text/css"  media="all" href="' . $fontLink . '" />';
            } else {
                $output = str_ireplace($this->basename($m[1]), 'orig_' . $this->basename($m[1]), $output);
            }
        }

        if (!empty($moveStyles)) {
            $moveStyles = '<noscript id="deferred-css">' . $moveStyles . '</noscript><script>'
                . 'var loadDeferredStyles = function() {'
                . 'var addStylesNode = document.getElementById("deferred-css");'
                . 'var replacement = document.createElement("div");'
                . 'replacement.innerHTML = addStylesNode.textContent;'
                . 'document.body.appendChild(replacement);'
                . 'addStylesNode.parentElement.removeChild(addStylesNode);'
                . '};'
                . 'window.addEventListener(\'load\', loadDeferredStyles);</script>';

            $output = str_ireplace('</body', $moveStyles . '</body', $output);
        }

        return true;
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

    /**
     * @return bool
     */
    public function isMoveForCurrentDevice()
    {
        return in_array($this->deviceDetector->getDeviceType(), $this->configProvider->getMoveFontForDevice());
    }
}
