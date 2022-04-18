<?php

namespace Amasty\Amp\Block\Product\Content\ProductList;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;

class Pure extends \Magento\Framework\View\Element\Template
{
    /**
     * @var mixed
     */
    private $block;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var string
     */
    private $name;

    public function __construct(
        Template\Context $context,
        ObjectManagerInterface $objectManager,
        $name = '',
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->objectManager = $objectManager;
        $this->name = $name;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!$this->block) {
            $this->block = $this->objectManager->create($this->name);
        }
        // @codingStandardsIgnoreLine
        return call_user_func_array([$this->block, $method], $args);
    }
}
