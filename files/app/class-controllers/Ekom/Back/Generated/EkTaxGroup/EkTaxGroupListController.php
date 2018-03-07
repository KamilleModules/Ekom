<?php

namespace Controller\Ekom\Back\Generated\EkTaxGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkTaxGroupListController extends EkomBackSimpleFormListController
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
            'table' => "ek_tax_group",
            'ric' => [
                "id",
            ],
            'label' => "tax group",
            'labelPlural' => "tax groups",
            'route' => "NullosAdmin_Ekom_Generated_EkTaxGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "tax groups",
                'breadcrumb' => "ek_tax_group",
                'form' => "ek_tax_group",
                'list' => "ek_tax_group",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new tax group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkTaxGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkTaxGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTaxGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
