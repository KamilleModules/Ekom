<?php

namespace Controller\Ekom\Back\Generated\EkProductHasFeature;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductHasFeatureListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductHasFeature_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$product_id = $this->getContextFromUrl('product_id');
		$table = "ek_product_has_feature";
		$context = [
			"product_id" => $product_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_product");
            $avatar = QuickPdo::fetch("
select $repr from `ek_product` where id=:product_id 
            ", [
				"product_id" => $product_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Features for product \"$avatar\"",
            'breadcrumb' => "ek_product_has_feature",
            'form' => "ek_product_has_feature",
            'list' => "ek_product_has_feature",
            'ric' => [
                'product_id',
                'feature_id',
            ],
            
            "newItemBtnText" => "Add a new feature for product \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductHasFeature_List") . "?form&product_id=$product_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProduct_List",             
            "buttons" => [
                [
                    "label" => "Back to product \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkProduct_List") . "?id=$product_id",
                ],
            ],
            "context" => [
            	"product_id" => $product_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}