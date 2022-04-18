<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Output;

interface OutputChainInterface
{
    /**
     * @param string $output
     *
     * @return bool
     */
    public function process(string &$output): bool;
}
