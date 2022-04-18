<?php

namespace Amasty\Amp\Block;

class Styles extends \Magento\Framework\View\Element\Template
{
    /**
     * @return string
     */
    public function getLinkColor()
    {
        return $this->getData('configProvider')->getLinkColor();
    }

    /**
     * @return string
     */
    public function getLinkColorFocus()
    {
        return $this->getData('configProvider')->getLinkColorFocus();
    }

    /**
     * @return string
     */
    public function getButtonBackground()
    {
        return $this->getData('configProvider')->getButtonBackground();
    }

    /**
     * @return string
     */
    public function getButtonBackgroundFocus()
    {
        return $this->getData('configProvider')->getButtonBackgroundFocus();
    }

    /**
     * @return string
     */
    public function getButtonTextColor()
    {
        return $this->getData('configProvider')->getButtonTextColor();
    }

    /**
     * @return string
     */
    public function getButtonTextColorFocus()
    {
        return $this->getData('configProvider')->getButtonTextColorFocus();
    }

    /**
     * @return string
     */
    public function getFooterBackground()
    {
        return $this->getData('configProvider')->getFooterBackground();
    }

    /**
     * @return string
     */
    public function getHeaderBackground()
    {
        return $this->getData('configProvider')->getHeaderBackground();
    }
}
