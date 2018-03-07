<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$tax_group_id = MorphicHelper::getFormContextValue("tax_group_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_tax_group_has_tax` h 
inner join ek_tax t on t.id=h.tax_id 
inner join ek_tax_group ta on ta.id=h.tax_group_id
where h.tax_group_id=$tax_group_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Tax group has taxes",
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
        '_action' => '',
    ],
    "headersVisibility" => [
        'tax_group_id' => false,
        'tax_id' => false,
    ],
    "realColumnMap" => [
        'tax' => [
            't.amount',
            't.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.tax_group_id',
        'h.tax_id',
        'h.order',
        'h.mode',
        'concat(t.id, ". ", t.amount) as tax',
    ],
    "ric" => [
        'tax_group_id',
        'tax_id',
    ],
    
    "formRouteExtraVars" => [               
        "tax_group_id" => $tax_group_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkTaxGroupHasTax_List",    
    'context' => $context,
];


