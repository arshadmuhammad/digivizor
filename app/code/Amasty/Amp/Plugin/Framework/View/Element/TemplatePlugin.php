<?php

namespace Amasty\Amp\Plugin\Framework\View\Element;

use Amasty\Amp\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class TemplatePlugin
{
    /**
     * @var bool
     */
    private $isAmpPage = null;

    /**
     * @var ConfigProvider
     */
    private $config;

    public function __construct(
        ConfigProvider $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Template $subject
     * @param $html
     *
     * @return string
     */
    public function afterToHtml(Template $subject, $html)
    {
        if ($this->getIsAmpPage($subject->getRequest()->getFullActionName())
            && (strpos($html, '<img') !== false
                || strpos($html, 'require(') !== false
                || strpos($html, 'text/x-magento-init') !== false
            )
        ) {
            $html = '';
        }

        return $html;
    }

    /**
     * @param string $action
     * @return bool
     */
    private function getIsAmpPage($action)
    {
        if ($this->isAmpPage === null) {
            $this->isAmpPage = $this->config->isAmpEnabledOnCurrentPage($action);
        }

        return (bool)$this->isAmpPage;
    }
}
