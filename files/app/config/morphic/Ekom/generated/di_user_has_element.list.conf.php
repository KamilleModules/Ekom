<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_user_has_element` h
inner join di_element `e` on `e`.id=h.element_id
inner join di_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user-elements",
    'table' => 'di_user_has_element',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_user_has_element',
    "headers" => [
        'user_id' => 'User id',
        'element_id' => 'Element id',
        'date_completed' => 'Date completed',
        'value' => 'Value',
        'element' => 'Element',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'element_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'element' => [
            'e.id',
            'e.type',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.element_id',
        'h.date_completed',
        'h.value',
        'concat( e.id, ". ", e.type ) as `element`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'user_id',
        'element_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUserHasElement_List",    
    'context' => $context,
];


