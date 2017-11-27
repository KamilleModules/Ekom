<?php


namespace Module\Ekom\Utils\Traits;


trait EkomContextAwareTrait{


    protected $shopId;
    protected $langId;
    protected $currencyId;



    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        return $this;
    }

    public function setLangId($langId)
    {
        $this->langId = $langId;
        return $this;
    }

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
        return $this;
    }

}