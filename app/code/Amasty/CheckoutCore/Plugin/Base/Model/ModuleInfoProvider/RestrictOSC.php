<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Plugin\Base\Model\ModuleInfoProvider;

use Amasty\Base\Model\ModuleInfoProvider;

class RestrictOSC
{
    /**
     * @param ModuleInfoProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetRestrictedModules(ModuleInfoProvider $subject, $result): array
    {
        $result[] = 'Amasty_Checkout';

        return $result;
    }
}
