<?php

namespace Controller\Ekom\Back\Generated\EkProductGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductGroupListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_group",
            'ric' => [
                "id",
            ],
            'label' => "product group",
            'labelPlural' => "product groups",
            'route' => "NullosAdmin_Ekom_Generated_EkProductGroup_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product groups",
                'breadcrumb' => "ek_product_group",
                'form' => "ek_product_group",
                'list' => "ek_product_group",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product group",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductGroup_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductGroup_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductGroup_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
