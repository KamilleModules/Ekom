<?php


namespace Module\Ekom\Back\WidgetModel\ContextBar;


use Module\Ekom\Api\Layer\CurrencyLayer;
use Module\Ekom\Api\Layer\LangLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Back\Util\QuickStartWizard\QuickStartWizard;

class ContextBarWidgetModel
{
    public static function getModel()
    {

        $shopIsSet = QuickStartWizard::isShopSet();
        if (true === $shopIsSet) {
            $currencies = CurrencyLayer::getCurrencyEntriesByShopId(EkomNullosUser::getEkomValue("shop_id"));
        } else {
            $currencies = "Veuillez d'abord choisir le shop";
        }


        return [
            'shopHost' => EkomNullosUser::getEkomValue("shop_host", null),
            'currencyIsoCode' => EkomNullosUser::getEkomValue("currency_iso_code", null),
            'langIsoCode' => EkomNullosUser::getEkomValue("lang_iso_code", null),
            //
            'currencyIsSet' => QuickStartWizard::isCurrencySet(),
            'shopIsSet' => $shopIsSet,
            'langIsSet' => QuickStartWizard::isLangSet(),
            'shops' => ShopLayer::getShopEntries(),
            'currencies' => $currencies,
            'languages' => LangLayer::getLangEntries(),
        ];
    }
}

