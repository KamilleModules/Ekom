<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class CountryLayer
{


    public static function getCountryItems()
    {
        return QuickPdo::fetchAll("
select id, iso_code from ek_country order by iso_code asc        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }



    /**
     * return useful list for feeding html select tags
     */
    public function getCountryList($langId = null, $isoCodeAsKey = false)
    {
        EkomApi::inst()->initWebContext();
        $langId = (null === $langId) ? (int)ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $isoCodeAsKey = (int)$isoCodeAsKey;
        return A::cache()->get("Ekom.CountryLayer.getCountryList.$langId.$isoCodeAsKey", function () use ($langId, $isoCodeAsKey) {


            if (0 === $isoCodeAsKey) {
                return EkomApi::inst()->countryLang()->readKeyValues("country_id", "label", [
                    "where" => [
                        ["lang_id", "=", $langId],
                    ],
                    "order" => [
                        "label" => "asc",
                    ],
                ]);
            } else {
                return QuickPdo::fetchAll("
select 
c.iso_code,
cl.label 
from ek_country_lang cl 
inner join ek_country c on c.id=cl.country_id  
where cl.lang_id=$langId
order by cl.label asc
", [], \PDO::FETCH_UNIQUE | \PDO::FETCH_COLUMN);
            }
        }, [
            "ek_country",
            "ek_country_lang",
        ]);
    }


    public static function getCountryIdByIso($iso)
    {
        return QuickPdo::fetch("select id from ek_country where iso_code=:iso", [
            "iso" => $iso,
        ], \PDO::FETCH_COLUMN);
    }


    public function getLabelByIso($iso, $langId = null)
    {
        $langId = E::getLangId($langId);
        return QuickPdo::fetch("
select cl.label
from ek_country_lang cl  
inner join ek_country c on c.id=cl.country_id
 
where c.iso_code=:iso
and cl.lang_id=$langId
", [
            "iso" => $iso,
        ], \PDO::FETCH_COLUMN);
    }
}
