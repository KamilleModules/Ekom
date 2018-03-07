<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$user_id = MorphicHelper::getFormContextValue("user_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `di_user_has_element` h 
inner join di_element e on e.id=h.element_id 
inner join di_user u on u.id=h.user_id
where h.user_id=$user_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User has elements",
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
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
        'element_id' => false,
    ],
    "realColumnMap" => [
        'element' => [
            'e.type',
            'e.varname',
            'e.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.element_id',
        'h.date_completed',
        'h.value',
        'concat(e.id, ". ", e.type) as element',
    ],
    "ric" => [
        'user_id',
        'element_id',
    ],
    
    "formRouteExtraVars" => [               
        "user_id" => $user_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUserHasElement_List",    
    'context' => $context,
];


