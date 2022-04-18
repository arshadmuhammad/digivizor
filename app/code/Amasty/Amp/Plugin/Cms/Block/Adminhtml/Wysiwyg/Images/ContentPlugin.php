<?php

namespace Amasty\Amp\Plugin\Cms\Block\Adminhtml\Wysiwyg\Images;

use Amasty\Amp\Plugin\Cms\Helper\Wysiwyg\ImagesPlugin;

class ContentPlugin
{
    /**
     * @param \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content $subject
     * @param string $url
     * @return string
     */
    public function afterGetOnInsertUrl(\Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content $subject, string $url)
    {
        $request = $subject->getRequest();
        $elementId = $request->getParam('target_element_id');
        if ($elementId && $elementId == 'cms_page_form_amp_content') {
            $url .= '?' . ImagesPlugin::AMP_IMAGE .'=1';
        }

        return $url;
    }
}
