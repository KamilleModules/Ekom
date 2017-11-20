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


    public function getLangIdByIso($iso3Letters)
    {
        return QuickPdo::fetch("select id from ek_lang where iso_code=:iso", [
            'iso' => $iso3Letters,
        ], \PDO::FETCH_COLUMN);
    }
}