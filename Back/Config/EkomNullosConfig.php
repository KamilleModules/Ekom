<?php


namespace Module\Ekom\Back\Config;

class EkomNullosConfig
{

    public static function getBreadcrumbItem($pageName)
    {
        $item = [
            "label" => "Unknown",
            "route" => "NullosAdmin_home",
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
            case "currency":
                $item = [
                    "label" => "Currency",
                    "route" => "NullosAdmin_Ekom_Currency_List",
                ];
                break;
            case "country_code":
                $item = [
                    "label" => "Country code",
                    "route" => "NullosAdmin_Ekom_CountryCode_List",
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
            default:
                break;
        }
        return $item;
    }

}