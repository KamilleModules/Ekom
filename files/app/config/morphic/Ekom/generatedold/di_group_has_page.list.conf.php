<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$group_id = MorphicHelper::getFormContextValue("group_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `di_group_has_page` h 
inner join di_group g on g.id=h.group_id 
inner join di_page p on p.id=h.page_id
where h.group_id=$group_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Group has pages",
    'table' => 'di_group_has_page',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_group_has_page',
    "headers" => [
        'group_id' => 'Group id',
        'page_id' => 'Page id',
        'position' => 'Position',
        'page' => 'Page',
        '_action' => '',
    ],
    "headersVisibility" => [
        'group_id' => false,
        'page_id' => false,
    ],
    "realColumnMap" => [
        'page' => [
            'p.name',
            'p.bg_document',
            'p.thumb',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.group_id',
        'h.page_id',
        'h.position',
        'concat(p.id, ". ", p.name) as page',
    ],
    "ric" => [
        'group_id',
        'page_id',
    ],
    
    "formRouteExtraVars" => [               
        "group_id" => $group_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiGroupHasPage_List",    
    'context' => $context,
];


