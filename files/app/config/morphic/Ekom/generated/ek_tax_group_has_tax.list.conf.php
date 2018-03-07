<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_tax_group_has_tax` h
inner join ek_tax `t` on `t`.id=h.tax_id
inner join ek_tax_group `ta` on `ta`.id=h.tax_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "tax group-taxes",
    'table' => 'ek_tax_group_has_tax',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_tax_group_has_tax',
    "headers" => [
        'tax_group_id' => 'Tax group id',
        'tax_id' => 'Tax id',
        'order' => 'Order',
        'mode' => 'Mode',
        'tax' => 'Tax',
        'tax_group' => 'Tax group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'tax_id' => false,
        'tax_group_id' => false,
    ],
    "realColumnMap" => [
        'tax' => [
            't.id',
            't.amount',
        ],
        'tax_group' => [
            'ta.id',
            'ta.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.tax_group_id',
        'h.tax_id',
        'h.order',
        'h.mode',
        'concat( t.id, ". ", t.amount ) as `tax`',
        'concat( ta.id, ". ", ta.label ) as `tax_group`',
    ],
    "ric" => [
        'tax_group_id',
        'tax_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkTaxGroupHasTax_List",    
    'context' => $context,
];


