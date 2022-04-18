<?php

namespace Amasty\Amp\Controller\Review;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class ListAjax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ReviewsGetter
     */
    private $reviewsGetter;

    public function __construct(
        Context $context,
        ReviewsGetter $reviewsGetter
    ) {
        parent::__construct($context);
        $this->reviewsGetter = $reviewsGetter;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $responseContent = [];
        try {
            $productId = (int)$this->_request->getParam('productId');
            $page = (int)$this->_request->getParam('p');
            $responseContent = $this->reviewsGetter->getResponseContent($productId, $page);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
