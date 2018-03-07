<?php

namespace Controller\Ekom\Back\Generated\EkInvoice;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkInvoiceListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "order_id", $_GET)) {        
            return $this->renderWithParent("ek_order", [
                "order_id" => $_GET["order_id"],
            ], [
                "order_id" => "id",
            ], [
                "order",
                "orders",
            ], "NullosAdmin_Ekom_Generated_EkOrder_List");
		} elseif ( array_key_exists ( "seller_id", $_GET)) {        
            return $this->renderWithParent("ek_seller", [
                "seller_id" => $_GET["seller_id"],
            ], [
                "seller_id" => "id",
            ], [
                "seller",
                "sellers",
            ], "NullosAdmin_Ekom_Generated_EkSeller_List");
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
            'table' => "ek_invoice",
            'ric' => [
                "id",
            ],
            'label' => "invoice",
            'labelPlural' => "invoices",
            'route' => "NullosAdmin_Ekom_Generated_EkInvoice_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "invoices",
                'breadcrumb' => "ek_invoice",
                'form' => "ek_invoice",
                'list' => "ek_invoice",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new invoice",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkInvoice_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkInvoice_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkInvoice_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
