<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Output;

interface OutputProcessorInterface
{
    /**
     * @param string &$output
     *
     * @return bool
     */
    public function process(string &$output): bool;
}
