<?php

namespace Controller\Ekom\Back\Generated\ProductCardHasTaxGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductCardHasTaxGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductCardHasTaxGroup_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$product_card_id = $this->getContextFromUrl('product_card_id');
		$table = "ek_product_card_has_tax_group";
		$context = [
			"product_card_id" => $product_card_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_product_card");
            $avatar = QuickPdo::fetch("
select $repr from `ek_product_card` where id=:product_card_id 
            ", [
				"product_card_id" => $product_card_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Tax groups for product card \"$avatar\"",
            'breadcrumb' => "product_card_has_tax_group",
            'form' => "product_card_has_tax_group",
            'list' => "product_card_has_tax_group",
            'ric' => [
                'product_card_id',
            ],
            
            "newItemBtnText" => "Add a new tax group for product card \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ProductCardHasTaxGroup_List") . "?form&product_card_id=$product_card_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductCard_List",             
            "buttons" => [
                [
                    "label" => "Back to product card \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_ProductCard_List") . "?id=$product_card_id",
                ],
            ],
            "context" => [
            	"product_card_id" => $product_card_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}