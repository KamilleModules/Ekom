<?php


namespace Module\Ekom\Api\Layer;


use QuickPdo\QuickPdo;

class LangLayer
{


    public function getLangIdByIso($iso3Letters)
    {
        return QuickPdo::fetch("select id from ek_lang where iso_code=:iso", [
            'iso' => $iso3Letters,
        ], \PDO::FETCH_COLUMN);
    }
}