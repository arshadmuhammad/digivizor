<?php

namespace Amasty\Amp\Plugin\Widget\Block\Adminhtml\Widget\Instance\Edit\Tab;

class SettingsPlugin
{
    /**
     * @param $subject
     * @param $widgets
     * @return array
     */
    public function afterGetTypesOptionsArray($subject, $widgets)
    {
        foreach ($widgets as $key => $widget) {
            if (strpos($widget['value'], 'amasty_amp') !== false) {
                unset($widgets[$key]);
            }
        }

        return $widgets;
    }
}
