<?php

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Block\Adminhtml\Settings;

use Magento\Config\Block\System\Config\Form\Field;

class CommonCheckbox extends Field
{
    protected function _renderInheritCheckbox(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }

    protected function _renderScopeLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }
}
