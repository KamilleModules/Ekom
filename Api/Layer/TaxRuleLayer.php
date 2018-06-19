<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class TaxRuleLayer
{


    public static function getIdByLabel(string $label)
    {
        return QuickPdo::fetch("select id from ek_tax_rule where label=:label", [
            "label" => $label,
        ], \PDO::FETCH_COLUMN);
    }


    public static function getLabel2Ids()
    {
        return QuickPdo::fetchAll("select label, id from ek_tax_rule", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

}