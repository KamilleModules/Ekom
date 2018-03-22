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
    public function getCountryList($isoCodeAsKey = false)
    {
        $isoCodeAsKey = (int)$isoCodeAsKey;
        return A::cache()->get("Ekom.CountryLayer.getCountryList.$isoCodeAsKey", function () use ($isoCodeAsKey) {


            $field = "iso_code";
            if (0 === $isoCodeAsKey) {
                $field = "id";
            }
            return QuickPdo::fetchAll("
select 
$field,
label 
from ek_country c   
order by label asc
", [], \PDO::FETCH_UNIQUE | \PDO::FETCH_COLUMN);
        });
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
