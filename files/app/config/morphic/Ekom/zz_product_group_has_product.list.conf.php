<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // productGroupId
$langId = (int)EkomNullosUser::getEkomValue("lang_id");


$q = "
select %s 
from ek_product_group_has_product h 
inner join ek_product_group g on g.id=h.product_group_id
inner join ek_product p on p.id=h.product_id 
left join ek_product_lang l on l.product_id=p.id
where 
h.product_group_id=$id
and l.lang_id=$langId
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product group's products",
    'table' => 'ek_product_group_has_product',
    'viewId' => 'product_group_has_product',
    'queryCols' => [
        'h.product_group_id',
        'h.product_id',
        'h.order',
        'concat (h.product_group_id, ". ", g.name) as product_group',
        'concat (p.id, ". ", l.label) as product',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_group_id' => "Product group id",
        'product_id' => "Product id",
        'order' => "Order",
        'product_group' => "Product group",
        'product' => "Product",
        '_action' => '',
    ],
    'headersVisibility' => [
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'product' => "l.label",
        'product_group' => "g.name",
    ],
    'ric' => [
        'product_group_id',
        'product_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductGroupHasProduct_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


