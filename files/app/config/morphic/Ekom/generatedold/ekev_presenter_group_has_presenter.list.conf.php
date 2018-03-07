<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$presenter_group_id = MorphicHelper::getFormContextValue("presenter_group_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ekev_presenter_group_has_presenter` h 
inner join ekev_presenter p on p.id=h.presenter_id 
inner join ekev_presenter_group pr on pr.id=h.presenter_group_id
where h.presenter_group_id=$presenter_group_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Presenter group has presenters",
    'table' => 'ekev_presenter_group_has_presenter',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_presenter_group_has_presenter',
    "headers" => [
        'presenter_group_id' => 'Presenter group id',
        'presenter_id' => 'Presenter id',
        'presenter' => 'Presenter',
        '_action' => '',
    ],
    "headersVisibility" => [
        'presenter_group_id' => false,
        'presenter_id' => false,
    ],
    "realColumnMap" => [
        'presenter' => [
            'p.first_name',
            'p.last_name',
            'p.pseudo',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.presenter_group_id',
        'h.presenter_id',
        'concat(p.id, ". ", p.first_name) as presenter',
    ],
    "ric" => [
        'presenter_group_id',
        'presenter_id',
    ],
    
    "formRouteExtraVars" => [               
        "presenter_group_id" => $presenter_group_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List",    
    'context' => $context,
];


