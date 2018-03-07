<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `app_user_info` h
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user infos",
    'table' => 'app_user_info',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'app_user_info',
    "headers" => [
        'user_id' => 'User id',
        'abo_leader_mail' => 'Abo leader mail',
        'abo_leader_partners_mail' => 'Abo leader partners mail',
        'abo_leader_sms' => 'Abo leader sms',
        'points_equipement' => 'Points equipement',
        'points_event' => 'Points event',
        'points_formation' => 'Points formation',
        'points_communication' => 'Points communication',
        'pro_type' => 'Pro type',
        'pro_secteur' => 'Pro secteur',
        'pro_secteur_autre' => 'Pro secteur autre',
        'pro_fonction' => 'Pro fonction',
        'b2b_company' => 'B2b company',
        'b2b_siret' => 'B2b siret',
        'b2b_tva' => 'B2b tva',
        'user_country' => 'User country',
        'alert_lf_points_catalog' => 'Alert lf points catalog',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
    ],
    "realColumnMap" => [
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.abo_leader_mail',
        'h.abo_leader_partners_mail',
        'h.abo_leader_sms',
        'h.points_equipement',
        'h.points_event',
        'h.points_formation',
        'h.points_communication',
        'h.pro_type',
        'h.pro_secteur',
        'h.pro_secteur_autre',
        'h.pro_fonction',
        'h.b2b_company',
        'h.b2b_siret',
        'h.b2b_tva',
        'h.user_country',
        'h.alert_lf_points_catalog',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'user_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_AppUserInfo_List",    
    'context' => $context,
];


