<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_training_session` h
inner join ektra_city `c` on `c`.id=h.city_id
inner join ektra_trainer_group `t` on `t`.id=h.trainer_group_id
inner join ektra_training `tr` on `tr`.id=h.training_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "training sessions",
    'table' => 'ektra_training_session',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_training_session',
    "headers" => [
        'id' => 'Id',
        'training_id' => 'Training id',
        'trainer_group_id' => 'Trainer group id',
        'city_id' => 'City id',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'is_default' => 'Is default',
        'capacity' => 'Capacity',
        'active' => 'Active',
        'city' => 'City',
        'trainer_group' => 'Trainer group',
        'training' => 'Training',
        '_action' => '',
    ],
    "headersVisibility" => [
        'city_id' => false,
        'trainer_group_id' => false,
        'training_id' => false,
    ],
    "realColumnMap" => [
        'city' => [
            'c.id',
            'c.label',
        ],
        'trainer_group' => [
            't.id',
            't.name',
        ],
        'training' => [
            'tr.id',
            'tr.prerequisites',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.training_id',
        'h.trainer_group_id',
        'h.city_id',
        'h.start_date',
        'h.end_date',
        'h.is_default',
        'h.capacity',
        'h.active',
        'concat( c.id, ". ", c.label ) as `city`',
        'concat( t.id, ". ", t.name ) as `trainer_group`',
        'concat( tr.id, ". ", tr.prerequisites ) as `training`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainingSession_List",    
    'context' => $context,
];


