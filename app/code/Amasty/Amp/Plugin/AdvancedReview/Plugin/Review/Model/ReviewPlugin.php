<?php

namespace Amasty\Amp\Plugin\AdvancedReview\Plugin\Review\Model;

use Amasty\Amp\Model\ConfigProvider;

class ReviewPlugin
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
     * @param $modelObject
     * @param $result
     * @return mixed
     */
    public function aroundAfterAggregate(
        $subject,
        \Closure $proceed,
        $modelObject,
        $result
    ) {
        if (!$this->config->isAmpProductPage()) {
            $result = $proceed($modelObject, $result);
        }

        return $result;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $modelObject
     * @param $result
     * @return mixed
     */
    public function aroundAfterValidate(
        $subject,
        \Closure $proceed,
        $modelObject,
        $result
    ) {
        if (!$this->config->isAmpProductPage()) {
            $result = $proceed($modelObject, $result);
        }

        return $result;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $modelObject
     */
    public function aroundBeforeSave(
        $subject,
        \Closure $proceed,
        $modelObject
    ) {
        if (!$this->config->isAmpProductPage()) {
            $proceed($modelObject);
        }
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $modelObject
     * @param $result
     * @return mixed
     */
    public function aroundAfterSave(
        $subject,
        \Closure $proceed,
        $modelObject,
        $result
    ) {
        if (!$this->config->isAmpProductPage()) {
            $result = $proceed($modelObject, $result);
        }

        return $result;
    }
}
