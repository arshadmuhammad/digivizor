<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Output;

class CheckIsOutputHtmlProcessor implements OutputProcessorInterface
{
    public function process(string &$output): bool
    {
        if (preg_match('/(<html[^>]*>)(?>.*?<body[^>]*>)/is', $output)) {
            if (preg_match('/(<\/body[^>]*>)(?>.*?<\/html[^>]*>)$/is', $output)) {
                return true;
            }
        }

        return false;
    }
}
