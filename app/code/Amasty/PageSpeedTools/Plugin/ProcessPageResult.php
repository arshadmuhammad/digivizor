<?php

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Plugin;

use Amasty\PageSpeedTools\Model\Output\OutputChainInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Event\ManagerInterface;

class ProcessPageResult
{
    const EVENT_NAME = 'amasty_pagespeed_after_process_page_result';

    /**
     * @var OutputChainInterface
     */
    private $outputChain;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        OutputChainInterface $outputChain,
        ManagerInterface $eventManager
    ) {
        $this->outputChain = $outputChain;
        $this->eventManager = $eventManager;
    }

    public function aroundRenderResult(
        ResultInterface $subject,
        \Closure $proceed,
        ResponseInterface $response
    ): ResultInterface {
        /** @var ResultInterface $result */
        $result = $proceed($response);
        $output = $response->getBody();

        if ($this->outputChain->process($output)) {
            $response->setBody($output);
        }

        $this->eventManager->dispatch(self::EVENT_NAME, ['response' => $response]);

        return $result;
    }
}
