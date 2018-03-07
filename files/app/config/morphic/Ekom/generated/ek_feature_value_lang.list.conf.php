<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_feature_value_lang` h
inner join ek_feature_value `f` on `f`.id=h.feature_value_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "feature value langs",
    'table' => 'ek_feature_value_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature_value_lang',
    "headers" => [
        'feature_value_id' => 'Feature value id',
        'lang_id' => 'Lang id',
        'value' => 'Value',
        'feature_value' => 'Feature value',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'feature_value_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'feature_value' => [
            'f.id',
            'f.feature_id',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.feature_value_id',
        'h.lang_id',
        'h.value',
        'concat( f.id, ". ", f.feature_id ) as `feature_value`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'feature_value_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeatureValueLang_List",    
    'context' => $context,
];


