<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_group_has_page` h
inner join di_group `g` on `g`.id=h.group_id
inner join di_page `p` on `p`.id=h.page_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "group-pages",
    'table' => 'di_group_has_page',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_group_has_page',
    "headers" => [
        'group_id' => 'Group id',
        'page_id' => 'Page id',
        'position' => 'Position',
        'group' => 'Group',
        'page' => 'Page',
        '_action' => '',
    ],
    "headersVisibility" => [
        'group_id' => false,
        'page_id' => false,
    ],
    "realColumnMap" => [
        'group' => [
            'g.id',
            'g.name',
        ],
        'page' => [
            'p.id',
            'p.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.group_id',
        'h.page_id',
        'h.position',
        'concat( g.id, ". ", g.name ) as `group`',
        'concat( p.id, ". ", p.name ) as `page`',
    ],
    "ric" => [
        'group_id',
        'page_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiGroupHasPage_List",    
    'context' => $context,
];


