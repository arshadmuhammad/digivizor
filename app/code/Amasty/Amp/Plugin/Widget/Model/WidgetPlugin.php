<?php

namespace Amasty\Amp\Plugin\Widget\Model;

class WidgetPlugin
{
    const CMS_PAGE_FORM_AMP_CONTENT = 'cms_page_form_amp_content';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param $subject
     * @param $widgets
     * @return array
     */
    public function afterGetWidgets($subject, $widgets)
    {
        if ($this->request->getParam('widget_target_id') == self::CMS_PAGE_FORM_AMP_CONTENT) {
            foreach ($widgets as $key => $widget) {
                if (strpos($key, 'amasty_amp') === false) {
                    unset($widgets[$key]);
                }
            }
        }

        return $widgets;
    }
}
