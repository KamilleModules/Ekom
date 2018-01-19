<?php


namespace Module\Ekom\Back\WidgetModel\ContextBar;


use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Back\Util\QuickStartWizard\QuickStartWizard;

class ContextBarWidgetModel
{
    public static function getModel()
    {
        return [
            'shopHost' => EkomNullosUser::getEkomValue("shop_host", null),
            'currencyIsoCode' => EkomNullosUser::getEkomValue("currency_iso_code", null),
            'langIsoCode' => EkomNullosUser::getEkomValue("lang_iso_code", null),
            //
            'currencyIsSet' => QuickStartWizard::isCurrencySet(),
            'shopIsSet' => QuickStartWizard::isShopSet(),
            'langIsSet' => QuickStartWizard::isLangSet(),
            'shops' => ShopLayer::getShopEntries(),
        ];
    }
}

