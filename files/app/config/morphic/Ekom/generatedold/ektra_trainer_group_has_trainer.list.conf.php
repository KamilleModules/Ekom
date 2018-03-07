<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$trainer_group_id = MorphicHelper::getFormContextValue("trainer_group_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ektra_trainer_group_has_trainer` h 
inner join ektra_trainer t on t.id=h.trainer_id 
inner join ektra_trainer_group tr on tr.id=h.trainer_group_id
where h.trainer_group_id=$trainer_group_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Trainer group has trainers",
    'table' => 'ektra_trainer_group_has_trainer',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_trainer_group_has_trainer',
    "headers" => [
        'trainer_group_id' => 'Trainer group id',
        'trainer_id' => 'Trainer id',
        'trainer' => 'Trainer',
        '_action' => '',
    ],
    "headersVisibility" => [
        'trainer_group_id' => false,
        'trainer_id' => false,
    ],
    "realColumnMap" => [
        'trainer' => [
            't.pseudo',
            't.first_name',
            't.last_name',
            't.phone',
            't.email',
            't.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.trainer_group_id',
        'h.trainer_id',
        'concat(t.id, ". ", t.pseudo) as trainer',
    ],
    "ric" => [
        'trainer_group_id',
        'trainer_id',
    ],
    
    "formRouteExtraVars" => [               
        "trainer_group_id" => $trainer_group_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainerGroupHasTrainer_List",    
    'context' => $context,
];


