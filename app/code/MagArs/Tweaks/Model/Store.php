<?php


namespace MagArs\Tweaks\Model;


class Store extends \Magento\Store\Model\Store {

    protected function _updatePathUseStoreView($url)
    {
        if ($this->isUseStoreInUrl()) {
            if($this->getCode() == 'us_en'){
                $url .= '/';
            }else{
                $url .= $this->getCode() . '/';
            }

        }
        return $url;
    }

}
