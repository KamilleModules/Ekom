<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class UserLayer
{

    public function getUserShippingAddresses($userId, $langId = null)
    {

        EkomApi::inst()->initWebContext();
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        $userId = (int)$userId;
        $langId = (int)$langId;


        return A::cache()->get("Module.Ekom.Api.Layer.UserLayer.getUserShippingAddresses.$langId.$userId", function () use ($userId, $langId) {


            return QuickPdo::fetchAll("
select 
a.id as address_id,        
a.first_name,        
a.last_name,        
a.phone,        
a.address,        
a.city,        
a.postcode,        
a.supplement,        
l.label as country


from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id

where 
h.user_id=$userId 
and h.type='shipping'
and a.active='1'
and l.lang_id=$langId


order by h.`order` asc 
        
        
        ");
        }, [
            "ek_user_has_address.$userId",
            "ek_country_lang.delete.*",
            "ek_country_lang.update.*",
        ]);
    }


    public function getUserGroupIds($userId)
    {
        return A::cache()->get("Module.Ekom.Api.Layer.UserLayer.getUserGroupIds.$userId", function () use ($userId) {

            $userId = (int)$userId;
            return EkomApi::inst()->userHasUserGroup()->readValues("user_group_id", [
                "where" => [
                    ["user_id", "=", $userId],
                ],
            ]);
        }, [
            "ek_user_has_user_group.delete.$userId",
            "ek_user_has_user_group.update.$userId",
        ]);
    }


    /**
     * @param $userId
     * @param null $langId
     * @return false|array representing the preferred shipping address:
     *
     *      - city
     *      - postcode
     *      - address
     *      - country_id
     *      - country_label
     *
     */
    public function getPreferredShippingAddress($userId, $langId = null)
    {

        //  todo : cache
        EkomApi::inst()->initWebContext();

        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $userId = (int)$userId;
        $langId = (int)$langId;


        return QuickPdo::fetch("
select 
a.city,
a.postcode,
a.address,
a.country_id,
l.label as country_label

from ek_user_has_address h 
inner join ek_address a on a.id=h.address_id 
inner join ek_country_lang l on l.country_id=a.country_id 

where h.user_id=$userId
and h.type='shipping'
and a.active=1
and l.lang_id=$langId

order by h.`order` asc


            
            ");
    }




    //--------------------------------------------
    //
    //--------------------------------------------


}