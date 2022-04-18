<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\GenerateQueue;
use Amasty\ImageOptimizerUi\Model\Image\ImageSetting;
use Amasty\ImageOptimizerUi\Model\Image\ResourceModel\CollectionFactory;
use Amasty\ImageOptimizerUi\Ui\DataProvider\Image\Form;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Start extends AbstractImageSettings
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GenerateQueue
     */
    private $generateQueue;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        GenerateQueue $generateQueue,
        ConfigProvider $configProvider,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->generateQueue = $generateQueue;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $imageSettingCollection = $this->collectionFactory->create();
        if ($imageSettingId = (int)$this->getRequest()->getParam(Form::IMAGE_SETTING_ID, 0)) {
            $imageSettingCollection->addFieldToFilter(ImageSetting::IMAGE_SETTING_ID, $imageSettingId);
        }
        $queueSize = $this->generateQueue->generateQueue($imageSettingCollection->getItems());

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
            'filesCount' => $queueSize,
            'filesPerRequest' => $this->configProvider->getImagesPerRequest()
        ]);
    }
}
