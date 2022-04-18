<?php

namespace Amasty\Amp\Block\Page\Html;

use Magento\Framework\View\Element\Template;

class Header extends \Magento\Theme\Block\Html\Header
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Amp::product/html/header.phtml';

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Amasty\Amp\Model\ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionFactory = $sessionFactory;
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function getCustomerFullName()
    {
        $customer = $this->getCustomerSession()->getCustomer();

        return trim($customer->getFirstname() . ' ' . $customer->getLastname());
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @return string
     */
    public function getLogoAlignment()
    {
        return $this->configProvider->getLogoAlignment();
    }
}
