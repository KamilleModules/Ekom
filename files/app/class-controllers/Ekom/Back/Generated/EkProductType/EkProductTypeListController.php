<?php

namespace Controller\Ekom\Back\Generated\EkProductType;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductTypeListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_type",
            'ric' => [
                "id",
            ],
            'label' => "product type",
            'labelPlural' => "product types",
            'route' => "NullosAdmin_Ekom_Generated_EkProductType_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product types",
                'breadcrumb' => "ek_product_type",
                'form' => "ek_product_type",
                'list' => "ek_product_type",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product type",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductType_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductType_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductType_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
