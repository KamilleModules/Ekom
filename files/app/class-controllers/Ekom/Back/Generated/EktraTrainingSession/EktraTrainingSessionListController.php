<?php

namespace Controller\Ekom\Back\Generated\EktraTrainingSession;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraTrainingSessionListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "city_id", $_GET)) {        
            return $this->renderWithParent("ektra_city", [
                "city_id" => $_GET["city_id"],
            ], [
                "city_id" => "id",
            ], [
                "city",
                "cities",
            ], "NullosAdmin_Ekom_Generated_EktraCity_List");
		} elseif ( array_key_exists ( "trainer_group_id", $_GET)) {        
            return $this->renderWithParent("ektra_trainer_group", [
                "trainer_group_id" => $_GET["trainer_group_id"],
            ], [
                "trainer_group_id" => "id",
            ], [
                "trainer group",
                "trainer groups",
            ], "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List");
		} elseif ( array_key_exists ( "training_id", $_GET)) {        
            return $this->renderWithParent("ektra_training", [
                "training_id" => $_GET["training_id"],
            ], [
                "training_id" => "id",
            ], [
                "training",
                "trainings",
            ], "NullosAdmin_Ekom_Generated_EktraTraining_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_training_session",
            'ric' => [
                "id",
            ],
            'label' => "training session",
            'labelPlural' => "training sessions",
            'route' => "NullosAdmin_Ekom_Generated_EktraTrainingSession_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "training sessions",
                'breadcrumb' => "ektra_training_session",
                'form' => "ektra_training_session",
                'list' => "ektra_training_session",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new training session",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraTrainingSession_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraTrainingSession_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTrainingSession_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
