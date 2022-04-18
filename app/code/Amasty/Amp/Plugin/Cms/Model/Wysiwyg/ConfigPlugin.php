<?php

namespace Amasty\Amp\Plugin\Cms\Model\Wysiwyg;

class ConfigPlugin
{
    const TINY_MCE_4 = 'mage/adminhtml/wysiwyg/tiny_mce/tinymce4Adapter';

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $encoder;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $url;

    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Amasty\Base\Model\Serializer $encoder,
        \Magento\Backend\Model\UrlInterface $url
    ) {
        $this->assetRepo = $assetRepo;
        $this->encoder = $encoder;
        $this->url = $url;
    }

    /**
     * @param \Magento\Cms\Model\Wysiwyg\Config $subject
     * @param \Closure $proceed
     * @param array $data
     *
     * @return \Magento\Framework\DataObject|mixed
     */
    public function aroundGetConfig(
        \Magento\Cms\Model\Wysiwyg\Config $subject,
        \Closure $proceed,
        $data = []
    ) {
        if (isset($data['is_amasty_amp_tab']) && $data['is_amasty_amp_tab']) {
            $config = $proceed($data);
            if ($subject->getConfig()->getData('activeEditorPath') == self::TINY_MCE_4) {
                $this->updateTinyMce4($config);
            }
        } else {
            $config = $proceed($data);
        }
        
        return $config;
    }

    /**
     * @param \Magento\Framework\DataObject $result
     */
    private function updateTinyMce4($result)
    {
        $settings = $result->getData('settings');

        if (!is_array($settings)) {
            $settings = [];
        }

        // @codingStandardsIgnoreStart
        $settings['plugins'] = 'advlist autolink code colorpicker directionality hr imagetools link media noneditable paste print table textcolor toc visualchars anchor charmap codesample contextmenu help image insertdatetime lists nonbreaking pagebreak preview searchreplace template textpattern visualblocks wordcount magentowidget amamp_image';
        $settings['toolbar1'] = 'formatselect | styleselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent';
        $settings['toolbar2'] = ' undo redo | link anchor table charmap | insertdatetime | searchreplace visualblocks  help | hr pagebreak';
        $settings['closed'] = '';
        $settings['forced_root_block'] = '';
        $settings['verify_html'] = 'false';
        $settings['cleanup'] = 'false';
        $settings['trim_span_elements'] = 'false';
        $settings['cleanup_on_startup'] = 'false';
        $settings['invalid_elements'] = 'iframe, frame, frameset, object, param, applet, embed, style, script, noscript, base, picture, video, audio';
        $settings['extended_valid_elements'] = 'div[*|**],button[*|**],amp-*[*|**]'; // Important! you must to insert tag's without whitespaces
        // @codingStandardsIgnoreEnd

        $result->setData('settings', $settings);
    }
}
