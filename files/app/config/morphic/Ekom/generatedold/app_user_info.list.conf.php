<?php 





$q = "select %s from `app_user_info`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User infos",
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
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'user_id',
        'abo_leader_mail',
        'abo_leader_partners_mail',
        'abo_leader_sms',
        'points_equipement',
        'points_event',
        'points_formation',
        'points_communication',
        'pro_type',
        'pro_secteur',
        'pro_secteur_autre',
        'pro_fonction',
        'b2b_company',
        'b2b_siret',
        'b2b_tva',
        'user_country',
        'alert_lf_points_catalog',
    ],
    "ric" => [
        'user_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_AppUserInfo_List",    
];


