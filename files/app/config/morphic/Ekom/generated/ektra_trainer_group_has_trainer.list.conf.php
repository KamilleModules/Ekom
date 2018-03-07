<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_trainer_group_has_trainer` h
inner join ektra_trainer `t` on `t`.id=h.trainer_id
inner join ektra_trainer_group `tr` on `tr`.id=h.trainer_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "trainer group-trainers",
    'table' => 'ektra_trainer_group_has_trainer',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_trainer_group_has_trainer',
    "headers" => [
        'trainer_group_id' => 'Trainer group id',
        'trainer_id' => 'Trainer id',
        'trainer' => 'Trainer',
        'trainer_group' => 'Trainer group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'trainer_id' => false,
        'trainer_group_id' => false,
    ],
    "realColumnMap" => [
        'trainer' => [
            't.id',
            't.pseudo',
        ],
        'trainer_group' => [
            'tr.id',
            'tr.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.trainer_group_id',
        'h.trainer_id',
        'concat( t.id, ". ", t.pseudo ) as `trainer`',
        'concat( tr.id, ". ", tr.name ) as `trainer_group`',
    ],
    "ric" => [
        'trainer_group_id',
        'trainer_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainerGroupHasTrainer_List",    
    'context' => $context,
];


