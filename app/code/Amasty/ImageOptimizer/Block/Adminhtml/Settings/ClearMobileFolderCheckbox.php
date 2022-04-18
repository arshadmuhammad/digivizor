<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Block\Adminhtml\Settings;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ClearMobileFolderCheckbox extends CommonCheckbox
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setData('value', __("Mobile Images Folder"));
        $element->setData('class', "amoptimizer-checkbox");
        $element->setData('name', "mobile");

        return parent::_getElementHtml($element);
    }
}
