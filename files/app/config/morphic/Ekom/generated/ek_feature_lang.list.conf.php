<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_feature_lang` h
inner join ek_feature `f` on `f`.id=h.feature_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "feature langs",
    'table' => 'ek_feature_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature_lang',
    "headers" => [
        'feature_id' => 'Feature id',
        'lang_id' => 'Lang id',
        'name' => 'Name',
        'feature' => 'Feature',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'feature_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'feature' => [
            'f.id',
            'f.id',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.feature_id',
        'h.lang_id',
        'h.name',
        'concat( f.id, ". ", f.id ) as `feature`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'lang_id',
        'feature_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeatureLang_List",    
    'context' => $context,
];


