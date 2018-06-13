<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class TaxRuleLayer
{

    public static function getLabel2Ids()
    {
        return QuickPdo::fetchAll("select label, id from ek_tax_rule", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

}