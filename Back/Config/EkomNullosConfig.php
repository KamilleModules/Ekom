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
            case "localization":
                $item = [
                    "label" => "Localization",
                    "route" => "NullosAdmin_Ekom_Currency_List",
                ];
                break;
            case "currency":
                $item = [
                    "label" => "Currency",
                    "route" => "NullosAdmin_Ekom_Currency_List",
                ];
                break;
            default:
                break;
        }
        return $item;
    }

}