<?php

namespace Controller\Ekom\Back\Generated\ShopHasProductHasTag;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ShopHasProductHasTagListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ShopHasProductHasTag_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$table = "ek_shop_has_product_has_tag";
		$context = [
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_shop");
            $avatar = QuickPdo::fetch("
select $repr from `ek_shop` where  
            ", [
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Tags for shop \"$avatar\"",
            'breadcrumb' => "shop_has_product_has_tag",
            'form' => "shop_has_product_has_tag",
            'list' => "shop_has_product_has_tag",
            'ric' => [
                'shop_has_product_shop_id',
                'shop_has_product_product_id',
                'tag_id',
            ],
            
            "newItemBtnText" => "Add a new tag for shop \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ShopHasProductHasTag_List") . "?form&",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShop_List",             
            "buttons" => [
                [
                    "label" => "Back to shop \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Shop_List") . "?",
                ],
            ],
            "context" => [
            				"avatar" => $avatar

            ],            
            
        ]);
    }


}