<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizerUi\Controller\Adminhtml\Image;

use Amasty\ImageOptimizer\Model\Image\ForceOptimization;
use Amasty\ImageOptimizer\Model\Queue\Queue;
use Amasty\ImageOptimizerUi\Controller\Adminhtml\AbstractImageSettings;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Process extends AbstractImageSettings
{
    /**
     * @var ForceOptimization
     */
    private $forceOptimization;

    public function __construct(
        ForceOptimization $forceOptimization,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->forceOptimization = $forceOptimization;
    }

    public function execute()
    {
        $limit = (int)$this->getRequest()->getParam('limit', 10);
        if (!$limit || $limit < 0) {
            $limit = 10;
        }
        $this->forceOptimization->execute($limit, [Queue::MANUAL]);

        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents(1);
    }
}
