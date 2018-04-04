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


    public static function getSokoAutocompleteMultipleDefaultOptions(array $options = [])
    {
        return array_replace([
            "addBtnText" => "Ajouter cet élément à la sélection",
            "removeBtnText" => "Supprimer",
            "emptyValueTitle" => "Attention!",
            "emptyValueText" => "La valeur ne peut pas être vide",
            "acceptDuplicate" => false,
            "duplicateValueTitle" => "Attention!",
            "duplicateValueText" => "Cette valeur a déjà été ajoutée",
        ], $options);
    }





    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getSokoAutocompleteServiceUri($action)
    {
        return "/service/Ekom/ecp/api?action=" . $action;
    }
}