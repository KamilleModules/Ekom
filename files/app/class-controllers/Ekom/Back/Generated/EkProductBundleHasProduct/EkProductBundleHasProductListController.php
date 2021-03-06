<?php

namespace Controller\Ekom\Back\Generated\EkProductBundleHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductBundleHasProductListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_id", $_GET)) {        
            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");
		} elseif ( array_key_exists ( "product_bundle_id", $_GET)) {        
            return $this->renderWithParent("ek_product_bundle", [
                "product_bundle_id" => $_GET["product_bundle_id"],
            ], [
                "product_bundle_id" => "id",
            ], [
                "product bundle",
                "product bundles",
            ], "NullosAdmin_Ekom_Generated_EkProductBundle_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_bundle_has_product",
            'ric' => [
                "product_bundle_id",
				"product_id",
            ],
            'label' => "product bundle-product",
            'labelPlural' => "product bundle-products",
            'route' => "NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product bundle-products",
                'breadcrumb' => "ek_product_bundle_has_product",
                'form' => "ek_product_bundle_has_product",
                'list' => "ek_product_bundle_has_product",
                'ric' => [
                    "product_bundle_id",
					"product_id",
                ],

                "newItemBtnText" => "Add a new product bundle-product",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
