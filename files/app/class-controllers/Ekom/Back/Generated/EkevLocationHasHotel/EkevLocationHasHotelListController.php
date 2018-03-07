<?php

namespace Controller\Ekom\Back\Generated\EkevLocationHasHotel;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevLocationHasHotelListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "hotel_id", $_GET)) {        
            return $this->renderWithParent("ekev_hotel", [
                "hotel_id" => $_GET["hotel_id"],
            ], [
                "hotel_id" => "id",
            ], [
                "hotel",
                "hotels",
            ], "NullosAdmin_Ekom_Generated_EkevHotel_List");
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
            'table' => "ekev_location_has_hotel",
            'ric' => [
                "location_id",
				"hotel_id",
            ],
            'label' => "location-hotel",
            'labelPlural' => "location-hotels",
            'route' => "NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "location-hotels",
                'breadcrumb' => "ekev_location_has_hotel",
                'form' => "ekev_location_has_hotel",
                'list' => "ekev_location_has_hotel",
                'ric' => [
                    "location_id",
					"hotel_id",
                ],

                "newItemBtnText" => "Add a new location-hotel",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
