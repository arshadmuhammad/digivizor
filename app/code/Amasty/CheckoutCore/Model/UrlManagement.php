<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model;

use Magento\Backend\Model\Url;

class UrlManagement extends Url
{
    /**
     * @inheritdoc
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $this->getRouteParamsResolver()->unsetData('route_params');

        return parent::getUrl($routePath, $routeParams);
    }
}
