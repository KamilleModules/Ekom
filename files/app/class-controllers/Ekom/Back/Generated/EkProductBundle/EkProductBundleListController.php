<?php

namespace Controller\Ekom\Back\Generated\EkProductBundle;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductBundleListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_bundle",
            'ric' => [
                "id",
            ],
            'label' => "product bundle",
            'labelPlural' => "product bundles",
            'route' => "NullosAdmin_Ekom_Generated_EkProductBundle_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product bundles",
                'breadcrumb' => "ek_product_bundle",
                'form' => "ek_product_bundle",
                'list' => "ek_product_bundle",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product bundle",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductBundle_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductBundle_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductBundle_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
