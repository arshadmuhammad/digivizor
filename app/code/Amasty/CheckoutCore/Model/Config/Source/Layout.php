<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Layout implements OptionSourceInterface
{
    const ONE_COLUMN = '1column';
    const TWO_COLUMNS = '2columns';
    const THREE_COLUMNS = '3columns';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::TWO_COLUMNS, 'label' => __('2 Columns')],
            ['value' => self::THREE_COLUMNS, 'label' => __('3 Columns')],
        ];
    }
}
