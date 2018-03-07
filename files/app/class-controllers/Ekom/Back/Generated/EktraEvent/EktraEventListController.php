<?php

namespace Controller\Ekom\Back\Generated\EktraEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraEventListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_id", $_GET)) {        
            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");
		} elseif ( array_key_exists ( "location_id", $_GET)) {        
            return $this->renderWithParent("ektra_location", [
                "location_id" => $_GET["location_id"],
            ], [
                "location_id" => "id",
            ], [
                "location",
                "locations",
            ], "NullosAdmin_Ekom_Generated_EktraLocation_List");
		} elseif ( array_key_exists ( "date_range_id", $_GET)) {        
            return $this->renderWithParent("ektra_date_range", [
                "date_range_id" => $_GET["date_range_id"],
            ], [
                "date_range_id" => "id",
            ], [
                "date range",
                "date ranges",
            ], "NullosAdmin_Ekom_Generated_EktraDateRange_List");
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
            'table' => "ektra_event",
            'ric' => [
                "id",
            ],
            'label' => "event",
            'labelPlural' => "events",
            'route' => "NullosAdmin_Ekom_Generated_EktraEvent_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "events",
                'breadcrumb' => "ektra_event",
                'form' => "ektra_event",
                'list' => "ektra_event",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new event",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraEvent_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraEvent_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraEvent_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
