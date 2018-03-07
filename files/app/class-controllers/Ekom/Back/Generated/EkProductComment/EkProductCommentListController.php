<?php

namespace Controller\Ekom\Back\Generated\EkProductComment;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductCommentListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		} elseif ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("ek_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_EkUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_comment",
            'ric' => [
                "id",
            ],
            'label' => "product comment",
            'labelPlural' => "product comments",
            'route' => "NullosAdmin_Ekom_Generated_EkProductComment_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product comments",
                'breadcrumb' => "ek_product_comment",
                'form' => "ek_product_comment",
                'list' => "ek_product_comment",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product comment",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductComment_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductComment_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductComment_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
