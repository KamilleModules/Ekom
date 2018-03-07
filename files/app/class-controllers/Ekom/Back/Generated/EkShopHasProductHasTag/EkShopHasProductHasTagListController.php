<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProductHasTag;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasProductHasTagListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( 
			array_key_exists ( "shop_id", $_GET) &&
			array_key_exists ( "product_id", $_GET)
		) {        
            return $this->renderWithParent("ek_shop_has_product", [
                "shop_id" => $_GET["shop_id"],
				"product_id" => $_GET["product_id"],
            ], [
                "shop_id" => "shop_id",
				"product_id" => "product_id",
            ], [
                "shop-product",
                "shop-products",
            ], "NullosAdmin_Ekom_Generated_EkShopHasProduct_List");
		} elseif ( array_key_exists ( "tag_id", $_GET)) {        
            return $this->renderWithParent("ek_tag", [
                "tag_id" => $_GET["tag_id"],
            ], [
                "tag_id" => "id",
            ], [
                "tag",
                "tags",
            ], "NullosAdmin_Ekom_Generated_EkTag_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_has_product_has_tag",
            'ric' => [
                "shop_id",
				"product_id",
				"tag_id",
            ],
            'label' => "shop-product-tag",
            'labelPlural' => "shop-product-tags",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-product-tags",
                'breadcrumb' => "ek_shop_has_product_has_tag",
                'form' => "ek_shop_has_product_has_tag",
                'list' => "ek_shop_has_product_has_tag",
                'ric' => [
                    "shop_id",
					"product_id",
					"tag_id",
                ],

                "newItemBtnText" => "Add a new shop-product-tag",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
