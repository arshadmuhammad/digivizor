<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CommonInfoField extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $columns = $this->getColspanHtmlAttr();

        return $this->_decorateRowHtml(
            $element,
            "<td class='amoptimizer-tooltip' colspan='{$columns}'>" . $this->toHtml() . '</td>'
        );
    }

    protected function getColspanHtmlAttr()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['website']) || isset($params['store'])) {
            return 5;
        }

        return 4;
    }

    protected function _renderInheritCheckbox(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }

    protected function _renderScopeLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }
}
