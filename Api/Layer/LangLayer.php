<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class LangLayer
{


    public static function getLangEntries()
    {
        return QuickPdo::fetchAll("
select id, iso_code from ek_lang order by id asc        
        ");
    }

    public static function getLangItems()
    {
        return QuickPdo::fetchAll("
select id, iso_code from ek_lang order by iso_code asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getIsoCodeById($langId)
    {
        $langId = (int)$langId;
        return QuickPdo::fetch("select iso_code from ek_lang where id=" . $langId, [], \PDO::FETCH_COLUMN);
    }

    public static function getLangIdByIso($iso3Letters)
    {
        return QuickPdo::fetch("select id from ek_lang where iso_code=:iso", [
            'iso' => $iso3Letters,
        ], \PDO::FETCH_COLUMN);
    }
}