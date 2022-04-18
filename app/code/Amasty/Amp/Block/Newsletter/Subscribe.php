<?php

namespace Amasty\Amp\Block\Newsletter;

class Subscribe extends \Magento\Newsletter\Block\Subscribe
{
    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return str_replace(['https:', 'http:'], '', parent::getFormActionUrl());
    }
}
