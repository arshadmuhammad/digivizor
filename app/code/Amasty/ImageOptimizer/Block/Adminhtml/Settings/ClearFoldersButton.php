<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Block\Adminhtml\Settings;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ClearFoldersButton extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_ImageOptimizerUi::clear_checkboxes_button.phtml';

    public function getActionUrl(): string
    {
        return $this->_urlBuilder->getUrl('amimageoptimizer/image/clearFolders');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->toHtml();
    }

    protected function _renderScopeLabel(AbstractElement $element): string
    {
        return '';
    }
}
