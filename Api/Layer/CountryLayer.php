<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class CountryLayer
{

    /**
     * return useful list for feeding html select tags
     */
    public function getCountryList($langId = null)
    {
        EkomApi::inst()->initWebContext();
        $langId = (null === $langId) ? (int)ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        return A::cache()->get("Ekom.CountryLayer.getCountryList.$langId", function () use ($langId) {
            return EkomApi::inst()->countryLang()->readKeyValues("country_id", "label", [
                "where" => [
                    ["lang_id", "=", $langId],
                ],
                "order" => [
                    "label" => "asc",
                ],
            ]);
        }, [
            "ek_country",
            "ek_country_lang",
        ]);
    }


    public function getCountryIdByIso($iso)
    {
        return QuickPdo::fetch("select id from ek_country where iso_code=:iso", [
            "iso" => $iso,
        ], \PDO::FETCH_COLUMN);
    }
}
