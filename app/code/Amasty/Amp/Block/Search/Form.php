<?php

namespace Amasty\Amp\Block\Search;

use Magento\Framework\View\Element\Template;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Search\Helper\Data
     */
    private $searchHelper;

    public function __construct(
        Template\Context $context,
        \Magento\Search\Helper\Data $searchHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->searchHelper = $searchHelper;
    }

    /**
     * @return string
     */
    public function getResultUrl()
    {
        return str_replace(['https:', 'http:'], '', $this->searchHelper->getResultUrl());
    }

    /**
     * @return \Magento\Search\Helper\Data
     */
    public function getHelper()
    {
        return $this->searchHelper;
    }
}
