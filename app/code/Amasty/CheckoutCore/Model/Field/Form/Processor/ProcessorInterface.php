<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form\Processor;

interface ProcessorInterface
{
    /**
     * @param array<int, array> $fields
     * @param int $storeId
     * @throws \Exception
     * @return array<int, array> Remaining fields that haven't been processed yet
     */
    public function process(array $fields, int $storeId): array;
}
