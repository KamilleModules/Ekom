<?php

namespace Controller\Ekom\Back\Generated\CategoryHasDiscount;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CategoryHasDiscountListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CategoryHasDiscount_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$category_id = $this->getContextFromUrl('category_id');
		$table = "ek_category_has_discount";
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
            'title' => "Discounts for category \"$avatar\"",
            'breadcrumb' => "category_has_discount",
            'form' => "category_has_discount",
            'list' => "category_has_discount",
            'ric' => [
                'category_id',
                'discount_id',
            ],
            
            "newItemBtnText" => "Add a new discount for category \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_CategoryHasDiscount_List") . "?form&category_id=$category_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCategory_List",             
            "buttons" => [
                [
                    "label" => "Back to category \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Category_List") . "?id=$category_id",
                ],
            ],
            "context" => [
            	"category_id" => $category_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}