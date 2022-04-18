<?php

declare(strict_types=1);

namespace Amasty\Amp\Model\Detection;

use Magento\Framework\ObjectManagerInterface;

class MobileDetect
{
    /**
     * @var \Zend_Http_UserAgent
     */
    private $userAgent;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Detection\MobileDetect|null
     */
    private $mobileDetector = null;

    public function __construct(
        \Zend_Http_UserAgent $userAgent,
        ObjectManagerInterface $objectManager
    ) {
        $this->userAgent = $userAgent;
        $this->objectManager = $objectManager;

        // We are using object manager to create 3rd-party packages' class
        if (class_exists(\Detection\MobileDetect::class)) {
            $this->mobileDetector = $this->objectManager->create(\Detection\MobileDetect::class);
        }
    }

    /**
     * @return bool
     */
    public function isMobile()
    {
        if ($this->mobileDetector) {
            return $this->mobileDetector->isMobile();
        }

        return stristr($this->userAgent->getUserAgent(), 'mobi') !== false;
    }
}
