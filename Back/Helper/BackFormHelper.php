<?php


namespace Module\Ekom\Back\Helper;

use Module\Ekom\Back\Config\EkomNullosConfig;
use Module\NullosAdmin\Utils\N;

class BackFormHelper
{
    public static function createSokoAutocompleteOptions(array $custom)
    {
        $action = $custom['action'];
        return [
            'action' => $action,
            'source' => self::getSokoAutocompleteServiceUri($action),
            /**
             * 0 is good because if the user has no idea of what she is looking for,
             * she can just press arrow down/up and be suggested the whole list...
             */
            'minLength' => 0,
        ];
    }





    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getSokoAutocompleteServiceUri($action)
    {
        return "/service/Ekom/ecp/api?action=" . $action;
    }
}