<?php

namespace Controller\Ekom\Back\Generated\ProductBundleHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductBundleHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductBundleHasProduct_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$product_bundle_id = $this->getContextFromUrl('product_bundle_id');
		$table = "ek_product_bundle_has_product";
		$context = [
			"product_bundle_id" => $product_bundle_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_product_bundle");
            $avatar = QuickPdo::fetch("
select $repr from `ek_product_bundle` where id=:product_bundle_id 
            ", [
				"product_bundle_id" => $product_bundle_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Products for product bundle \"$avatar\"",
            'breadcrumb' => "product_bundle_has_product",
            'form' => "product_bundle_has_product",
            'list' => "product_bundle_has_product",
            'ric' => [
                'product_bundle_id',
                'product_id',
            ],
            
            "newItemBtnText" => "Add a new product for product bundle \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ProductBundleHasProduct_List") . "?form&product_bundle_id=$product_bundle_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductBundle_List",             
            "buttons" => [
                [
                    "label" => "Back to product bundle \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_ProductBundle_List") . "?id=$product_bundle_id",
                ],
            ],
            "context" => [
            	"product_bundle_id" => $product_bundle_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}