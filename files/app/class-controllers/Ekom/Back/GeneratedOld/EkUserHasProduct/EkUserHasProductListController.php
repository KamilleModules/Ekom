<?php

namespace Controller\Ekom\Back\Generated\EkUserHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkUserHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkUserHasProduct_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$user_id = $this->getContextFromUrl('user_id');
		$table = "ek_user_has_product";
		$context = [
			"user_id" => $user_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_user");
            $avatar = QuickPdo::fetch("
select $repr from `ek_user` where id=:user_id 
            ", [
				"user_id" => $user_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Products for user \"$avatar\"",
            'breadcrumb' => "ek_user_has_product",
            'form' => "ek_user_has_product",
            'list' => "ek_user_has_product",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new product for user \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkUserHasProduct_List") . "?form&user_id=$user_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkUser_List",             
            "buttons" => [
                [
                    "label" => "Back to user \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkUser_List") . "?id=$user_id",
                ],
            ],
            "context" => [
            	"user_id" => $user_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}