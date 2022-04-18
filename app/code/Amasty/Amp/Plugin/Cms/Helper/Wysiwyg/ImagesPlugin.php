<?php

namespace Amasty\Amp\Plugin\Cms\Helper\Wysiwyg;

class ImagesPlugin
{
    const AMP_IMAGE = 'amp_image';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Magento\Cms\Helper\Wysiwyg\Images $subject
     * @param string $html
     * @return string
     */
    public function afterGetImageHtmlDeclaration(\Magento\Cms\Helper\Wysiwyg\Images $subject, string $html)
    {
        if ($this->request->getParam(self::AMP_IMAGE)) {
            $textForReplace = '<amp-img class="amamp-image -wysiwyg" width="200" height="200" layout="fixed"';
            $html = str_replace('<img', $textForReplace, $html . '</amp-img>');
        }

        return $html;
    }
}
