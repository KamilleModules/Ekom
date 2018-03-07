<?php

namespace Controller\Ekom\Back\Generated\EktraTrainerGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraTrainerGroupListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_trainer_group",
            'ric' => [
                "id",
            ],
            'label' => "trainer group",
            'labelPlural' => "trainer groups",
            'route' => "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "trainer groups",
                'breadcrumb' => "ektra_trainer_group",
                'form' => "ektra_trainer_group",
                'list' => "ektra_trainer_group",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new trainer group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraTrainerGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
