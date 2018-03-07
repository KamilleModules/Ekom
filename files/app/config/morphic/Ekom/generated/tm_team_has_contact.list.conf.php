<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `tm_team_has_contact` h
inner join tm_contact `c` on `c`.id=h.contact_id
inner join tm_team `t` on `t`.id=h.team_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "team-contacts",
    'table' => 'tm_team_has_contact',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'tm_team_has_contact',
    "headers" => [
        'team_id' => 'Team id',
        'contact_id' => 'Contact id',
        'contact' => 'Contact',
        'team' => 'Team',
        '_action' => '',
    ],
    "headersVisibility" => [
        'contact_id' => false,
        'team_id' => false,
    ],
    "realColumnMap" => [
        'contact' => [
            'c.id',
            'c.name',
        ],
        'team' => [
            't.id',
            't.mailtype',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.team_id',
        'h.contact_id',
        'concat( c.id, ". ", c.name ) as `contact`',
        'concat( t.id, ". ", t.mailtype ) as `team`',
    ],
    "ric" => [
        'team_id',
        'contact_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TmTeamHasContact_List",    
    'context' => $context,
];


