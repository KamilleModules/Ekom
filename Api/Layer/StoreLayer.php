<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class StoreLayer
{


    public static function hasStoreByLabel(string $label)
    {
        $res = QuickPdo::fetch("
select id from ek_store where label=:label        
        ", [
            "label" => $label,
        ]);
        if (false !== $res) {
            return true;
        }
        return false;
    }
}
