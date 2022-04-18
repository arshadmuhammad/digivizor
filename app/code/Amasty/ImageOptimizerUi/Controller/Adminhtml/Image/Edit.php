<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizerUi\Api\ImageSettingRepositoryInterface;
use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;
use Amasty\ImageOptimizerUi\Ui\DataProvider\Image\Form;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Edit extends AbstractImageSettings
{
    /**
     * @var ImageSettingRepositoryInterface
     */
    private $repository;

    public function __construct(
        ImageSettingRepositoryInterface $repository,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ImageOptimizer::image_settings');

        if ($imageSettingId = (int) $this->getRequest()->getParam(Form::IMAGE_SETTING_ID)) {
            try {
                $this->repository->getById($imageSettingId);
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Image Folder Settings'));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This image settings no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Pattern For Image Folder Optimization'));
        }

        return $resultPage;
    }
}
