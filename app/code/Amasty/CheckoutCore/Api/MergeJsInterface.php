<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Api;

interface MergeJsInterface
{
    /**
     * @param string[] $fileNames
     * @return boolean
     */
    public function createBundle(array $fileNames);
}
