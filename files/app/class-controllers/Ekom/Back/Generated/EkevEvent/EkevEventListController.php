<?php

namespace Controller\Ekom\Back\Generated\EkevEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevEventListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "location_id", $_GET)) {        
            return $this->renderWithParent("ekev_location", [
                "location_id" => $_GET["location_id"],
            ], [
                "location_id" => "id",
            ], [
                "location",
                "locations",
            ], "NullosAdmin_Ekom_Generated_EkevLocation_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_event",
            'ric' => [
                "id",
            ],
            'label' => "event",
            'labelPlural' => "events",
            'route' => "NullosAdmin_Ekom_Generated_EkevEvent_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "events",
                'breadcrumb' => "ekev_event",
                'form' => "ekev_event",
                'list' => "ekev_event",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new event",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEvent_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEvent_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
