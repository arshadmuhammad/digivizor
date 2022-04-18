<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons\Image;

use Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons\GenericButton;
use Amasty\ImageOptimizerUi\Ui\DataProvider\Image\Form;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        if (!$this->getImageSettingId()) {
            return [];
        }
        $alertMessage = __('Are you sure you want to do this?');
        $onClick = sprintf('deleteConfirm("%s", "%s")', $alertMessage, $this->getDeleteUrl());

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'id' => 'image-setting-edit-delete-button',
            'on_click' => $onClick,
            'sort_order' => 20,
        ];
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', [Form::IMAGE_SETTING_ID => $this->getImageSettingId()]);
    }

    public function getImageSettingId(): int
    {
        return (int)$this->request->getParam(Form::IMAGE_SETTING_ID);
    }
}
