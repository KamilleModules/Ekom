<?php

namespace Controller\Ekom\Back\Generated\EkProductHasFeature;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductHasFeatureListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "feature_id", $_GET)) {        
            return $this->renderWithParent("ek_feature", [
                "feature_id" => $_GET["feature_id"],
            ], [
                "feature_id" => "id",
            ], [
                "feature",
                "features",
            ], "NullosAdmin_Ekom_Generated_EkFeature_List");
		} elseif ( array_key_exists ( "product_id", $_GET)) {        
            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");
		} elseif ( array_key_exists ( "feature_value_id", $_GET)) {        
            return $this->renderWithParent("ek_feature_value", [
                "feature_value_id" => $_GET["feature_value_id"],
            ], [
                "feature_value_id" => "id",
            ], [
                "feature value",
                "feature values",
            ], "NullosAdmin_Ekom_Generated_EkFeatureValue_List");
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
            'table' => "ek_product_has_feature",
            'ric' => [
                "product_id",
				"feature_id",
				"shop_id",
            ],
            'label' => "product-feature",
            'labelPlural' => "product-features",
            'route' => "NullosAdmin_Ekom_Generated_EkProductHasFeature_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product-features",
                'breadcrumb' => "ek_product_has_feature",
                'form' => "ek_product_has_feature",
                'list' => "ek_product_has_feature",
                'ric' => [
                    "product_id",
					"feature_id",
					"shop_id",
                ],

                "newItemBtnText" => "Add a new product-feature",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductHasFeature_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductHasFeature_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductHasFeature_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
