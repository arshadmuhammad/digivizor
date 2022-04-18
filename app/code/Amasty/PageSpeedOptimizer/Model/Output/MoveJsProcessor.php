<?php

namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;

class MoveJsProcessor implements OutputProcessorInterface
{
    /**
     * @var \Amasty\PageSpeedOptimizer\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Amasty\PageSpeedOptimizer\Model\Js\ScriptsExtractor
     */
    private $scriptsExtractor;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Amasty\PageSpeedOptimizer\Model\ConfigProvider $configProvider,
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\PageSpeedOptimizer\Model\Js\ScriptsExtractor $scriptsExtractor
    ) {
        $this->configProvider = $configProvider;
        $this->scriptsExtractor = $scriptsExtractor;
        $this->request = $request;
    }

    public function process(string &$output): bool
    {
        if ($this->configProvider->isMoveJS() && $this->scriptsExtractor->canProcessPage()
            && !$this->request->getParam('amoptimizer_not_move')
        ) {
            [$output, $scripts] = $this->scriptsExtractor->extract($output, true);
            $output = str_ireplace('</body', implode('', $scripts) . '</body', $output);
        }

        return true;
    }
}
