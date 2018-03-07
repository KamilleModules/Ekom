<?php

namespace Controller\Ekom\Back\Generated\EktraCity;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraCityListController extends EkomBackSimpleFormListController
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
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_city",
            'ric' => [
                "id",
            ],
            'label' => "city",
            'labelPlural' => "cities",
            'route' => "NullosAdmin_Ekom_Generated_EktraCity_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "cities",
                'breadcrumb' => "ektra_city",
                'form' => "ektra_city",
                'list' => "ektra_city",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new city",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraCity_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraCity_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraCity_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
