<?php

namespace Amasty\Amp\Plugin\Framework\Data\Form\FormKey;

class ValidatorPlugin
{
    /**
     * @var \Amasty\Amp\Model\UrlConfigProvider
     */
    private $configProvider;

    public function __construct(\Amasty\Amp\Model\UrlConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Framework\Data\Form\FormKey\Validator $subject
     * @param \Closure $proceed
     * @param $request
     * @return bool|mixed
     */
    public function aroundValidate(
        \Magento\Framework\Data\Form\FormKey\Validator $subject,
        \Closure $proceed,
        $request
    ) {
        return $this->validateAmp($request) ? true : $proceed($request);
    }

    /**
     * @param $request
     * @return $request
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function validateAmp($request)
    {
        return $request->getHeader('Origin') == $this->configProvider->getAmpCacheUrl()
            || $request->getHeader('Amp-Same-Origin') === 'true';
    }
}
