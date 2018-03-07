<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_event` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ektra_location `l` on `l`.id=h.location_id
inner join ektra_date_range `d` on `d`.id=h.date_range_id
inner join ektra_trainer_group `t` on `t`.id=h.trainer_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "events",
    'table' => 'ektra_event',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_event',
    "headers" => [
        'id' => 'Id',
        'product_id' => 'Product id',
        'location_id' => 'Location id',
        'date_range_id' => 'Date range id',
        'trainer_group_id' => 'Trainer group id',
        'shop_id' => 'Shop id',
        'product' => 'Product',
        'location' => 'Location',
        'date_range' => 'Date range',
        'trainer_group' => 'Trainer group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'location_id' => false,
        'date_range_id' => false,
        'trainer_group_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'location' => [
            'l.id',
            'l.name',
        ],
        'date_range' => [
            'd.id',
            'd.shop_id',
        ],
        'trainer_group' => [
            't.id',
            't.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.product_id',
        'h.location_id',
        'h.date_range_id',
        'h.trainer_group_id',
        'h.shop_id',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( l.id, ". ", l.name ) as `location`',
        'concat( d.id, ". ", d.shop_id ) as `date_range`',
        'concat( t.id, ". ", t.name ) as `trainer_group`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraEvent_List",    
    'context' => $context,
];


