<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_lang` h 
inner join ek_lang l on l.id=h.lang_id 
inner join ek_shop s on s.id=h.shop_id
where h.shop_id=$shop_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has langs",
    'table' => 'ek_shop_has_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_lang',
    "headers" => [
        'lang_id' => 'Lang id',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.label',
            'l.iso_code',
            'l.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.lang_id',
        'concat(l.id, ". ", l.label) as lang',
    ],
    "ric" => [
        'lang_id',
    ],
    
    "formRouteExtraVars" => [               
        "shop_id" => $shop_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasLang_List",    
    'context' => $context,
];


