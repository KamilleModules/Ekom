<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;


class EkShopHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
        if (array_key_exists("shop_id", $_GET)) {
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");


        } elseif (array_key_exists("product_id", $_GET)) {

            return $this->renderWithParent("ek_product", [
                "product_id" => $_GET["product_id"],
            ], [
                "product_id" => "id",
            ], [
                "product",
                "products",
            ], "NullosAdmin_Ekom_Generated_EkProduct_List");

        } elseif (array_key_exists("seller_id", $_GET)) {


            return $this->renderWithParent("ek_seller", [
                "seller_id" => $_GET["seller_id"],
            ], [
                "seller_id" => "id",
            ], [
                "seller",
                "sellers",
            ], "NullosAdmin_Ekom_Generated_EkSeller_List");


        } elseif (array_key_exists("product_type_id", $_GET)) {

            return $this->renderWithParent("ek_product_type", [
                "product_type_id" => $_GET["product_type_id"],
            ], [
                "product_type_id" => "id",
            ], [
                "product type",
                "product types",
            ], "NullosAdmin_Ekom_Generated_EkProductType_List");

        } elseif (array_key_exists("manufacturer_id", $_GET)) {

            return $this->renderWithParent("ek_manufacturer", [
                "manufacturer_id" => $_GET["manufacturer_id"],
            ], [
                "manufacturer_id" => "id",
            ], [
                "manufacturer",
                "manufacturers",
            ], "NullosAdmin_Ekom_Generated_EkManufacturer_List");
        }


        return $this->renderWithNoParent();

    }


    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_has_product",
            'ric' => [
                "shop_id",
                "product_id",
            ],
            'label' => "shop-product",
            'labelPlural' => "shop-products",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasProduct_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }


    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-products",
                'breadcrumb' => "ek_shop_has_product",
                'form' => "ek_shop_has_product",
                'list' => "ek_shop_has_product",
                'ric' => [
                    'shop_id',
                    'product_id',
                ],

                "newItemBtnText" => "Add a new shop-product",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProduct_List") . "?form",

                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShop_List",
                "context" => [],
            ]);
        } else {
            throw new \Exception("not permitted");
        }
    }

}