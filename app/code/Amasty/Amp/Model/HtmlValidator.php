<?php

namespace Amasty\Amp\Model;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class HtmlValidator implements ArgumentInterface
{
    /**
     * @var array
     */
    private $prohibitedTags = [
        '<script',
        '<noscript',
        '<base',
        '<img',
        '<picture',
        '<video',
        '<audio',
        '<iframe',
        '<frame',
        '<frameset',
        '<object',
        '<param',
        '<applet',
        '<embed',
        '<frame',
        '<style',
    ];

    /**
     * @var array
     */
    private $prohibitedAttributes = [
        '(href="javascript:[^`"]*")' => 'href="#"',
        '(_self)' => '_blank',
        '(_parent)' => '_blank',
        '(_top)' => '_blank',
        '(target="")' => 'target="_blank"',
        '(action=)' => 'action-xhr=',
    ];

    /**
     * @param $html
     * @return string
     */
    public function getValidHtml($html)
    {
        $html = $this->removeForbiddenContent($html);
        $html = $this->removeForbiddenAttributes($html);

        return $html;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function removeForbiddenContent($content)
    {
        foreach ($this->prohibitedTags as $tag) {
            if (strpos($content, $tag) !== false) {
                $tag = trim($tag, '<');
                $content = preg_replace(
                    '#(\<' . $tag . '[^\>]*\>)(.*?)(\<\/' . $tag . '\>)|(\<' . $tag . '[^\>]*)(.*?)\>#ims',
                    '',
                    $content
                );
            }
        }

        return $content;
    }

    /**
     * @param $content
     * @return string
     */
    private function removeForbiddenAttributes($content)
    {
        foreach ($this->prohibitedAttributes as $attribute => $replace) {
            $content = preg_replace($attribute, $replace, $content);
        }

        return $content;
    }
}
