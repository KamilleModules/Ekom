<?php

namespace Controller\Ekom\Back\Generated\EktraLocation;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraLocationListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "country_id", $_GET)) {        
            return $this->renderWithParent("ek_country", [
                "country_id" => $_GET["country_id"],
            ], [
                "country_id" => "id",
            ], [
                "country",
                "countries",
            ], "NullosAdmin_Ekom_Generated_EkCountry_List");
		} elseif ( array_key_exists ( "shop_id", $_GET)) {        
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
            'table' => "ektra_location",
            'ric' => [
                "id",
            ],
            'label' => "location",
            'labelPlural' => "locations",
            'route' => "NullosAdmin_Ekom_Generated_EktraLocation_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "locations",
                'breadcrumb' => "ektra_location",
                'form' => "ektra_location",
                'list' => "ektra_location",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new location",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraLocation_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraLocation_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraLocation_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
