<?php

namespace Amasty\Amp\Block\Product\Content\View\Review;

class Form extends \Magento\Review\Block\Form
{
    /**
     * Initialize review form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Amasty_Amp::product/content/view/review/form.phtml');
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return str_replace(['https:', 'http:'], '', parent::getAction());
    }
}
