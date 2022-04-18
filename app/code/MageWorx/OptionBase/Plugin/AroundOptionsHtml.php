<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Plugin;

use \Magento\Catalog\Block\Product\View\Options;
use \Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use \Zend\Stdlib\StringWrapper\MbString;
use MageWorx\OptionBase\Model\Product\Option\AdditionalHtmlData;

/**
 * This plugin adds option_id to html elements.
 */
class AroundOptionsHtml
{
    /**
     * @var MbString
     */
    protected $mbString;

    /**
     * @var AdditionalHtmlData
     */
    protected $additionalHtmlData;

    /**
     * These nodes should be found and filled
     * before return to the page.
     *
     * @var array
     */
    protected $voidNodes = [
        'textarea'
    ];

    /**
     * @param MbString $mbString
     * @param AdditionalHtmlData $additionalHtmlData
     * @param State $state
     */
    public function __construct(
        MbString $mbString,
        AdditionalHtmlData $additionalHtmlData,
        State $state

    ) {
        $this->mbString           = $mbString;
        $this->additionalHtmlData = $additionalHtmlData;
        $this->state              = $state;
    }


    /**
     * @param Options $subject
     * @param \Closure $proceed
     * @param Option $option
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetOptionHtml(Options $subject, \Closure $proceed, Option $option)
    {
        $result                  = $proceed($option);
        $dom                     = new \DOMDocument();
        $dom->preserveWhiteSpace = false;

        $this->mbString->setEncoding('UTF-8', 'html-entities');
        $result = $this->mbString->convert($result);

        libxml_use_internal_errors(true);
        $dom->loadHTML($result);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // find and prepare void nodes
        $query = '//*[not(node())]';
        $nodes = $xpath->query($query);

        foreach ($nodes as $node) {
            if (in_array($node->nodeName, $this->voidNodes)) {
                // Fill the found node with 'NOT_VOID' comment. Remove this comment later before return $result.
                $node->appendChild(new \DOMComment('NOT_VOID'));
            }
        }

        $fileScript = '';
        if ($option->getType() == Option::OPTION_TYPE_FILE &&
            $this->state->getAreaCode() == Area::AREA_ADMINHTML)
        {
            $fileScriptNodeValue = $xpath->query('//script')->item(0)->nodeValue;
            $fileScript = '<script>' .  $fileScriptNodeValue . '</script>';
            $fileScript = $this->cDataReplacer($fileScript);
        }

        $xpath->query('//div')->item(0)->setAttribute("data-option_id", $option->getOptionId());

        foreach ($this->additionalHtmlData->getData() as $additionalHtmlItem) {
            $additionalHtmlItem->getAdditionalHtml($dom, $option);
        }

        $resultBody = $dom->getElementsByTagName('body')->item(0);
        $result     = $fileScript . $this->getInnerHtml($resultBody, $option);
        return str_replace('<!--NOT_VOID-->', '', $result);
    }

    /**
     * @param \DOMElement $node
     * @param Option $option
     * @return string
     */
    protected function getInnerHtml(\DOMElement $node, Option $option)
    {
        $innerHTML = '';
        $children  = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);

            if (strpos($innerHTML, '</script>') !== false) {
                $innerHTML = str_replace(
                    ['<script type="text/x-magento-init"><![CDATA[', ']]></script>'],
                    ['<script type="text/x-magento-init">', '</script>'],
                    $innerHTML
                );
                if ($option->getType() == Option::OPTION_TYPE_DATE ||
                    $option->getType() == Option::OPTION_TYPE_DATE_TIME ||
                    $option->getType() == Option::OPTION_TYPE_TIME ||
                    $option->getType() == Option::OPTION_TYPE_FILE
                ) {
                    $innerHTML = $this->cDataReplacer($innerHTML);
                }
            }
        }

        return $innerHTML;
    }

    /**
     * @param $subject
     * @return mixed
     */
    protected function cDataReplacer($subject)
    {
        $searchParams = [
            '<![CDATA[',
            ']]>'
        ];

        return str_replace($searchParams, '', $subject);
    }

}
