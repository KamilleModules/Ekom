<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_presenter_group_has_presenter` h
inner join ekev_presenter `p` on `p`.id=h.presenter_id
inner join ekev_presenter_group `pr` on `pr`.id=h.presenter_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "presenter group-presenters",
    'table' => 'ekev_presenter_group_has_presenter',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_presenter_group_has_presenter',
    "headers" => [
        'presenter_group_id' => 'Presenter group id',
        'presenter_id' => 'Presenter id',
        'presenter' => 'Presenter',
        'presenter_group' => 'Presenter group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'presenter_id' => false,
        'presenter_group_id' => false,
    ],
    "realColumnMap" => [
        'presenter' => [
            'p.id',
            'p.first_name',
        ],
        'presenter_group' => [
            'pr.id',
            'pr.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.presenter_group_id',
        'h.presenter_id',
        'concat( p.id, ". ", p.first_name ) as `presenter`',
        'concat( pr.id, ". ", pr.name ) as `presenter_group`',
    ],
    "ric" => [
        'presenter_group_id',
        'presenter_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevPresenterGroupHasPresenter_List",    
    'context' => $context,
];


