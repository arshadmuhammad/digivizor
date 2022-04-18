<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Config;

use Amasty\CheckoutCore\Api\CheckoutBlocksProviderInterface;
use Amasty\CheckoutCore\Model\Config\Source\Layout;
use Amasty\CheckoutCore\ViewModel\StyleSwitcherProvider;

class CheckoutBlocksProvider implements CheckoutBlocksProviderInterface
{
    /**
     * @var StyleSwitcherProvider
     */
    private $styleSwitcherProvider;

    public function __construct(StyleSwitcherProvider $styleSwitcherProvider)
    {
        $this->styleSwitcherProvider = $styleSwitcherProvider;
    }

    /**
     * @return array
     */
    public function getDefaultBlockTitles(): array
    {
        return [
            'shipping_address' => __('Shipping Address'),
            'shipping_method' => __('Shipping Method'),
            'delivery' => __('Delivery'),
            'payment_method' => __('Payment Method'),
            'summary' => __('Order Summary'),
        ];
    }

    /**
     * Method for provide default Layout Builder configs.
     * $store used in submodule.
     *
     * @param ?int $store
     * @return array
     */
    public function getBlocksConfig(int $store = null): array
    {
        if ($this->styleSwitcherProvider->getDesignLayout() === Layout::THREE_COLUMNS) {
            return $this->getThreeColumnsDefaultLayout();
        }

        return $this->getTwoColumnsDefaultLayout();
    }

    /**
     * @return array
     */
    private function getTwoColumnsDefaultLayout(): array
    {
        return [
            [
                [
                    'name' => 'shipping_address',
                    'title' => '',
                ],
                [
                    'name' => 'shipping_method',
                    'title' => '',
                ],
                [
                    'name' => 'delivery',
                    'title' => '',
                ]
            ],
            [
                [
                    'name' => 'payment_method',
                    'title' => '',
                ],
                [
                    'name' => 'summary',
                    'title' => '',
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    private function getThreeColumnsDefaultLayout(): array
    {
        return [
            [
                [
                    'name' => 'shipping_address',
                    'title' => '',
                ],
            ],
            [
                [
                    'name' => 'shipping_method',
                    'title' => '',
                ],
                [
                    'name' => 'delivery',
                    'title' => '',
                ],
                [
                    'name' => 'payment_method',
                    'title' => '',
                ]
            ],
            [
                [
                    'name' => 'summary',
                    'title' => '',
                ]
            ]
        ];
    }
}
