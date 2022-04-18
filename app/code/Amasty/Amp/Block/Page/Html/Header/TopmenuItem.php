<?php

namespace Amasty\Amp\Block\Page\Html\Header;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;

class TopmenuItem extends Template implements ArgumentInterface
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Amp::page/html/header/topmenu_item.phtml';

    /**
     * @var \Amasty\Amp\Model\UrlConfigProvider
     */
    private $urlConfigProvider;

    public function __construct(
        Template\Context $context,
        \Amasty\Amp\Model\UrlConfigProvider $urlConfigProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlConfigProvider = $urlConfigProvider;
    }

    /**
     * @return string
     */
    public function getChildItemsHtml()
    {
        $html = '';
        foreach ($this->getItem()->getChildren() as $child) {
            $this->setItem($child);
            $html .= $this->toHtml();
        }

        return $html;
    }

    /**
     * @param array|string $data
     * @param null $allowedTags
     *
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $data = htmlspecialchars_decode($data, ENT_QUOTES);
        return parent::escapeHtml($data, $allowedTags);
    }

    /**
     * @param string $url
     * @return string
     */
    public function convertToAmpUrl($url)
    {
        return $this->urlConfigProvider->convertToAmpUrl($url);
    }
}
