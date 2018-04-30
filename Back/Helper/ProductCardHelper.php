<?php


namespace Module\Ekom\Back\Helper;


use Module\Ekom\Utils\E;

class ProductCardHelper
{

    public static function getVerticalMenuItem(string $tabId, string $label, array $options = [])
    {


        $tab = $options['tab'] ?? null;
        $isDisabled = $options['isDisabled'] ?? false;
        $productCardId = $options['productCardId'] ?? null;


        $args = [
            't' => $tabId,
        ];
        if ($productCardId) {
            $args['id'] = $productCardId;
        }

        return [
            $label,
            E::link("Ekom_Catalog_Product_Form") . "?" . http_build_query($args),
            ($tabId === $tab),
            $isDisabled,
        ];
    }

    public static function getHorizontalMenuItem(string $tabId, string $label, array $options = [])
    {

        $tab = $options['tab'] ?? null;
        $isDisabled = $options['isDisabled'] ?? false;


        $args = [
            'form' => '1',
            't' => 'products',
            't2' => $tabId,
        ];
        if (array_key_exists('product_type', $_GET)) {
            $args['product_type'] = $_GET['product_type'];
        }
        if (array_key_exists('id', $_GET)) {
            $args['id'] = $_GET['id'];
        } elseif (array_key_exists('productCardId', $options)) {
            $args['id'] = $options['productCardId'];
        }
        if (array_key_exists('product_id', $_GET)) {
            $args['product_id'] = $_GET['product_id'];
        } elseif (array_key_exists('productId', $options)) {
            $args['product_id'] = $options['productId'];
        }

        return [
            $label,
            E::link("Ekom_Catalog_Product_Form") . "?" . http_build_query($args),
            ($tabId === $tab),
            $isDisabled,
        ];
    }
}