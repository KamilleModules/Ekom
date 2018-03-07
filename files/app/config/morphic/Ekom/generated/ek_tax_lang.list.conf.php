<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_tax_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_tax `t` on `t`.id=h.tax_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "tax langs",
    'table' => 'ek_tax_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_tax_lang',
    "headers" => [
        'tax_id' => 'Tax id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'lang' => 'Lang',
        'tax' => 'Tax',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'tax_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'tax' => [
            't.id',
            't.amount',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.tax_id',
        'h.lang_id',
        'h.label',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( t.id, ". ", t.amount ) as `tax`',
    ],
    "ric" => [
        'tax_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkTaxLang_List",    
    'context' => $context,
];


