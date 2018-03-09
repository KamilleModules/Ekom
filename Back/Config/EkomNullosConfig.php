<?php


namespace Module\Ekom\Back\Config;

use Core\Services\A;

class EkomNullosConfig
{

    public static function getBreadcrumbItem($pageName)
    {



        $item = [
            "label" => "Unknown ($pageName)",
            "label" => ucfirst($pageName),
            "route" => A::route(),
        ];

        switch ($pageName) {
            case "admin":
                $item = [
                    "label" => "Admin",
                    "route" => null,
                ];
                break;
            case "app_sanitycheck":
                $item = [
                    "label" => "App sanity check",
                    "route" => null,
                ];
                break;
            case "cache_manager":
                $item = [
                    "label" => "Cache Manager",
                    "route" => null,
                ];
                break;
            case "currency":
                $item = [
                    "label" => "Currency",
                    "route" => "NullosAdmin_Ekom_Currency_List",
                ];
                break;
            case "country":
                $item = [
                    "label" => "Country",
                    "route" => "NullosAdmin_Ekom_Country_List",
                ];
                break;
            case "country_translation":
                $item = [
                    "label" => "Country translation",
                    "route" => "NullosAdmin_Ekom_CountryTranslation_List",
                ];
                break;
            case "feature":
                $item = [
                    "label" => "Feature",
                    "route" => false,
                ];
                break;
            case "feature_lang":
                $item = [
                    "label" => "Feature lang",
                    "route" => false,
                ];
                break;
            case "feature_value":
                $item = [
                    "label" => "Feature value",
                    "route" => false,
                ];
                break;
            case "lang":
                $item = [
                    "label" => "Lang",
                    "route" => "NullosAdmin_Ekom_Lang_List",
                ];
                break;
            case "localization":
                $item = [
                    "label" => "Localization",
                    "route" => "NullosAdmin_Ekom_Currency_List",
                ];
                break;
            case "payment_method":
                $item = [
                    "label" => "Payment method",
                    "route" => "NullosAdmin_Ekom_PaymentMethod_List",
                ];
                break;
            case "product_attribute":
                $item = [
                    "label" => "Product attribute",
                    "route" => false,
                ];
                break;
            case "product_attribute_translation":
                $item = [
                    "label" => "Product attribute translation",
                    "route" => false,
                ];
                break;
            case "product":
                $item = [
                    "label" => "Product",
                    "route" => false,
                ];
                break;
            case "product_card":
                $item = [
                    "label" => "Product card",
                    "route" => false,
                ];
                break;
            case "product_card_lang":
                $item = [
                    "label" => "Product card lang",
                    "route" => false,
                ];
                break;
            case "product_comment":
                $item = [
                    "label" => "Product comment",
                    "route" => false,
                ];
                break;
            case "product_group":
                $item = [
                    "label" => "Product group",
                    "route" => false,
                ];
                break;
            case "product_group_has_product":
                $item = [
                    "label" => "Product group has product",
                    "route" => false,
                ];
                break;
            case "product_has_product_attribute":
                $item = [
                    "label" => "Product has attribute",
                    "route" => false,
                ];
                break;
            case "product_has_feature":
                $item = [
                    "label" => "Product has feature",
                    "route" => false,
                ];
                break;
            case "product_lang":
                $item = [
                    "label" => "Product lang",
                    "route" => false,
                ];
                break;
            case "shop_has_product_lang":
                $item = [
                    "label" => "Shop has product translation",
                    "route" => "NullosAdmin_Ekom_ShopHasProductLang_List",
                ];
                break;
            case "shop_has_product":
                $item = [
                    "label" => "Shop has product",
                    "route" => "NullosAdmin_Ekom_ShopHasProduct_List",
                ];
                break;
            case "shop_has_product_has_tag":
                $item = [
                    "label" => "Shop has product tag",
                    "route" => "NullosAdmin_Ekom_ShopHasProductTag_List",
                ];
                break;
            case "shopmix":
                $item = [
                    "label" => "Shop Mix",
                    "route" => "NullosAdmin_Ekom_ShopMix",
                ];
                break;
            case "shopmix_currency":
                $item = [
                    "label" => "Shop Mix Currency Form",
                    "route" => "NullosAdmin_Ekom_ShopMix_CurrencyForm",
                ];
                break;
            case "tag":
                $item = [
                    "label" => "tag",
                    "route" => "NullosAdmin_Ekom_Tag_List",
                ];
                break;
            case "tax":
                $item = [
                    "label" => "Tax",
                    "route" => false,
                ];
                break;
            case "tax_translation":
                $item = [
                    "label" => "Tax Translation",
                    "route" => false,
                ];
                break;
            case "user":
                $item = [
                    "label" => "User",
                    "route" => false,
                ];
                break;
            case "user_address":
                $item = [
                    "label" => "User address",
                    "route" => false,
                ];
                break;
            case "user_has_address":
                $item = [
                    "label" => "User has address",
                    "route" => false,
                ];
                break;
            case "user_has_user_group":
                $item = [
                    "label" => "User has group",
                    "route" => false,
                ];
                break;
            case "user_group":
                $item = [
                    "label" => "User group",
                    "route" => false,
                ];
                break;
            default:
                break;
        }
        return $item;
    }

}