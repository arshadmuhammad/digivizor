<?php

declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizer\Model\Image\CheckTools;
use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;
use Amasty\ImageOptimizerUi\Model\Image\Repository;
use Amasty\ImageOptimizerUi\Ui\DataProvider\Image\Form;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractImageSettings
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CheckTools
     */
    private $checkTools;

    public function __construct(
        Context $context,
        Repository $repository,
        DataPersistorInterface $dataPersistor,
        CheckTools $checkTools
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->dataPersistor = $dataPersistor;
        $this->checkTools = $checkTools;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            try {
                $imageSettingId = 0;
                if ($imageSettingId = (int)$this->getRequest()->getParam(Form::IMAGE_SETTING_ID)) {
                    $model = $this->repository->getById($imageSettingId);
                } else {
                    $model = $this->repository->getEmptyImageSettingModel();
                }

                $model->addData($data);
                foreach ($this->checkTools->check($model) as $toolError) {
                    $this->messageManager->addWarningMessage($toolError);
                }

                $model->setFolders($model->getFolders());
                $this->repository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the item.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', [Form::IMAGE_SETTING_ID => $model->getId()]);
                }

                if ($this->getRequest()->getParam('save_and_optimize')) {
                    $this->dataPersistor->set(Form::OPTIMIZE, true);
                    return $this->_redirect(
                        '*/*/edit',
                        [Form::IMAGE_SETTING_ID => $model->getId()]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set(Form::IMAGE_SETTING_DATA, $data);
                if ($imageSettingId) {
                    return $this->_redirect('*/*/edit', [Form::IMAGE_SETTING_ID => $imageSettingId]);
                } else {
                    return $this->_redirect('*/*/create');
                }
            }
        }
        return $this->_redirect('*/*/');
    }
}
