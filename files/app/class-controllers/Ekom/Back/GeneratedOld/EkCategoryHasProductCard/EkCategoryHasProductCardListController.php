<?php

namespace Controller\Ekom\Back\Generated\EkCategoryHasProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCategoryHasProductCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$category_id = $this->getContextFromUrl('category_id');
		$table = "ek_category_has_product_card";
		$context = [
			"category_id" => $category_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_category");
            $avatar = QuickPdo::fetch("
select $repr from `ek_category` where id=:category_id 
            ", [
				"category_id" => $category_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Product cards for category \"$avatar\"",
            'breadcrumb' => "ek_category_has_product_card",
            'form' => "ek_category_has_product_card",
            'list' => "ek_category_has_product_card",
            'ric' => [
                'category_id',
                'product_card_id',
            ],
            
            "newItemBtnText" => "Add a new product card for category \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List") . "?form&category_id=$category_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCategory_List",             
            "buttons" => [
                [
                    "label" => "Back to category \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkCategory_List") . "?id=$category_id",
                ],
            ],
            "context" => [
            	"category_id" => $category_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}