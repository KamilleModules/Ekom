<?php 





$q = "select %s from `ektra_training_session`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Training sessions",
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
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'training_id',
        'trainer_group_id',
        'city_id',
        'start_date',
        'end_date',
        'is_default',
        'capacity',
        'active',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainingSession_List",    
];


