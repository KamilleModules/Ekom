<?php

namespace Controller\Ekom\Back\Generated\EkevPresenterGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevPresenterGroupListController extends EkomBackSimpleFormListController
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
            'table' => "ekev_presenter_group",
            'ric' => [
                "id",
            ],
            'label' => "presenter group",
            'labelPlural' => "presenter groups",
            'route' => "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "presenter groups",
                'breadcrumb' => "ekev_presenter_group",
                'form' => "ekev_presenter_group",
                'list' => "ekev_presenter_group",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new presenter group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevPresenterGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
