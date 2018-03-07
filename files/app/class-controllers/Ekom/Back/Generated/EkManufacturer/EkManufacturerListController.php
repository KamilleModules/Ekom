<?php

namespace Controller\Ekom\Back\Generated\EkManufacturer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkManufacturerListController extends EkomBackSimpleFormListController
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
            'table' => "ek_manufacturer",
            'ric' => [
                "id",
            ],
            'label' => "manufacturer",
            'labelPlural' => "manufacturers",
            'route' => "NullosAdmin_Ekom_Generated_EkManufacturer_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "manufacturers",
                'breadcrumb' => "ek_manufacturer",
                'form' => "ek_manufacturer",
                'list' => "ek_manufacturer",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new manufacturer",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkManufacturer_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkManufacturer_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkManufacturer_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
