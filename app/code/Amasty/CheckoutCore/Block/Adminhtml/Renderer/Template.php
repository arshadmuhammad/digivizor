<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Block\Adminhtml\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Template extends \Magento\Backend\Block\Template implements RendererInterface
{
    /**
     * @var AbstractElement
     */
    protected $_element;

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function isStoreSelected()
    {
        return $this->_request->getParam('store', false) !== false;
    }
}
