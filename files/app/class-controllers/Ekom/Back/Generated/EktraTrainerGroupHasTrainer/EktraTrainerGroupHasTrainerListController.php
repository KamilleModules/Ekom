<?php

namespace Controller\Ekom\Back\Generated\EktraTrainerGroupHasTrainer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraTrainerGroupHasTrainerListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "trainer_id", $_GET)) {        
            return $this->renderWithParent("ektra_trainer", [
                "trainer_id" => $_GET["trainer_id"],
            ], [
                "trainer_id" => "id",
            ], [
                "trainer",
                "trainers",
            ], "NullosAdmin_Ekom_Generated_EktraTrainer_List");
		} elseif ( array_key_exists ( "trainer_group_id", $_GET)) {        
            return $this->renderWithParent("ektra_trainer_group", [
                "trainer_group_id" => $_GET["trainer_group_id"],
            ], [
                "trainer_group_id" => "id",
            ], [
                "trainer group",
                "trainer groups",
            ], "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_trainer_group_has_trainer",
            'ric' => [
                "trainer_group_id",
				"trainer_id",
            ],
            'label' => "trainer group-trainer",
            'labelPlural' => "trainer group-trainers",
            'route' => "NullosAdmin_Ekom_Generated_EktraTrainerGroupHasTrainer_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "trainer group-trainers",
                'breadcrumb' => "ektra_trainer_group_has_trainer",
                'form' => "ektra_trainer_group_has_trainer",
                'list' => "ektra_trainer_group_has_trainer",
                'ric' => [
                    "trainer_group_id",
					"trainer_id",
                ],

                "newItemBtnText" => "Add a new trainer group-trainer",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraTrainerGroupHasTrainer_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraTrainerGroupHasTrainer_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTrainerGroupHasTrainer_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
