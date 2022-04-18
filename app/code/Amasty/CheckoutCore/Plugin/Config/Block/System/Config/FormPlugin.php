<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Plugin\Config\Block\System\Config;

use Amasty\CheckoutCore\Block\Adminhtml\System\Config\Expander;
use Magento\Config\Block\System\Config\Form;

class FormPlugin
{
    /**
     * @param Form $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(Form $subject, $result)
    {
        if ($subject->getRequest()->getParam('expand')) {
            $layout = $subject->getLayout();
            $blockExpander = $layout->createBlock(Expander::class);
            $result = $result . $blockExpander->toHtml();
        }

        return $result;
    }
}
