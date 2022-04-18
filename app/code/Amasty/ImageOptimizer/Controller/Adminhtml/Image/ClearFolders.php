<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class ClearFolders extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ImageOptimizer::config';

    /**
     * @var \Amasty\ImageOptimizer\Model\Image\ClearFolders
     */
    private $clearFolders;

    public function __construct(
        Context $context,
        \Amasty\ImageOptimizer\Model\Image\ClearFolders $clearFolders
    ) {
        parent::__construct($context);
        $this->clearFolders = $clearFolders;
    }

    public function execute()
    {
        $folders = $this->getRequest()->getParam('folders', []);
        try {
            foreach ($folders as $folderType) {
                $this->clearFolders->execute($folderType);
            }
            $message = count($folders) > 1
                ? 'Folders were successfully cleared.'
                : 'Folder was successfully cleared';
            $result = ['isError' => false, 'message' => __($message)->render()];
        } catch (LocalizedException $exception) {
            $result = ['isError' => true, 'message' => $exception->getMessage()];
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
