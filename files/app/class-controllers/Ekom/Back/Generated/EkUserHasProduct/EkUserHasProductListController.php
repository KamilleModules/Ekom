<?php

namespace Controller\Ekom\Back\Generated\EkUserHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkUserHasProductListController extends EkomBackSimpleFormListController
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
            'table' => "ek_user_has_product",
            'ric' => [
                "id",
            ],
            'label' => "user-product",
            'labelPlural' => "user-products",
            'route' => "NullosAdmin_Ekom_Generated_EkUserHasProduct_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user-products",
                'breadcrumb' => "ek_user_has_product",
                'form' => "ek_user_has_product",
                'list' => "ek_user_has_product",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new user-product",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkUserHasProduct_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkUserHasProduct_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkUserHasProduct_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
