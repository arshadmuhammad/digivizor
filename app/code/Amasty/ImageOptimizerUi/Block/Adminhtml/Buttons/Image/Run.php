<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons\Image;

use Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Run extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Run Optimization'),
            'class' => 'primary',
            'id' => 'image-optimization-run-button',
            'on_click' => 'var registry = require("uiRegistry");'
                . 'registry.get("amimageoptimizer_image_listing.amimageoptimizer_image_listing.modal").toggleModal();'
                . 'registry.get("amimageoptimizer_image_listing.'
                . 'amimageoptimizer_image_listing.modal.optimization").start()'
        ];
    }
}
