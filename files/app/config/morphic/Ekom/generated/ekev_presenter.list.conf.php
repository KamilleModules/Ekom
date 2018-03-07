<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_presenter` h
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "presenters",
    'table' => 'ekev_presenter',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_presenter',
    "headers" => [
        'id' => 'Id',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'pseudo' => 'Pseudo',
        'shop_id' => 'Shop id',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.first_name',
        'h.last_name',
        'h.pseudo',
        'h.shop_id',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevPresenter_List",    
    'context' => $context,
];


