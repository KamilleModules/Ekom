<?php

namespace Controller\Ekom\Back\Generated\ProductCardCombinationHasProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductCardCombinationHasProductCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductCardCombinationHasProductCard_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$product_card_combination_id = $this->getContextFromUrl('product_card_combination_id');
		$table = "ecc_product_card_combination_has_product_card";
		$context = [
			"product_card_combination_id" => $product_card_combination_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ecc_product_card_combination");
            $avatar = QuickPdo::fetch("
select $repr from `ecc_product_card_combination` where id=:product_card_combination_id 
            ", [
				"product_card_combination_id" => $product_card_combination_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Product cards for product card combination \"$avatar\"",
            'breadcrumb' => "product_card_combination_has_product_card",
            'form' => "product_card_combination_has_product_card",
            'list' => "product_card_combination_has_product_card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new product card for product card combination \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ProductCardCombinationHasProductCard_List") . "?form&product_card_combination_id=$product_card_combination_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EccProductCardCombination_List",             
            "buttons" => [
                [
                    "label" => "Back to product card combination \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_ProductCardCombination_List") . "?id=$product_card_combination_id",
                ],
            ],
            "context" => [
            	"product_card_combination_id" => $product_card_combination_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}