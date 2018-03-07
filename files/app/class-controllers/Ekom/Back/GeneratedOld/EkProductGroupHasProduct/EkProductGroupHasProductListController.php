<?php

namespace Controller\Ekom\Back\Generated\EkProductGroupHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductGroupHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$product_group_id = $this->getContextFromUrl('product_group_id');
		$table = "ek_product_group_has_product";
		$context = [
			"product_group_id" => $product_group_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_product_group");
            $avatar = QuickPdo::fetch("
select $repr from `ek_product_group` where id=:product_group_id 
            ", [
				"product_group_id" => $product_group_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Products for product group \"$avatar\"",
            'breadcrumb' => "ek_product_group_has_product",
            'form' => "ek_product_group_has_product",
            'list' => "ek_product_group_has_product",
            'ric' => [
                'product_group_id',
                'product_id',
            ],
            
            "newItemBtnText" => "Add a new product for product group \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List") . "?form&product_group_id=$product_group_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductGroup_List",             
            "buttons" => [
                [
                    "label" => "Back to product group \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProductGroup_List") . "?id=$product_group_id",
                ],
            ],
            "context" => [
            	"product_group_id" => $product_group_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}