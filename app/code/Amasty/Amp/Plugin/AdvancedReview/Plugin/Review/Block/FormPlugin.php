<?php

namespace Amasty\Amp\Plugin\AdvancedReview\Plugin\Review\Block;

use Amasty\Amp\Model\ConfigProvider;

class FormPlugin
{
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
     * @param $subject
     * @param \Closure $proceed
     * @param $formObject
     * @param $result
     * @return mixed
     */
    public function aroundAfterToHtml(
        $subject,
        \Closure $proceed,
        $formObject,
        $result
    ) {
        if (!$this->config->isAmpProductPage()) {
            $result = $proceed($formObject, $result);
        }

        return $result;
    }
}
