<?php


namespace Module\Ekom\Api\Layer;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * ShopProductCatalogLayer::removeProduct($productId);
 * ShopProductCatalog::removeCard();
 * GeneralProductCatalog::removeProduct();
 * GeneralProductCatalog::removeCard();
 */
class ShopProductCatalogLayer
{

    /**
     * remove an entry from ek_shop_has_product
     * which will by cascading effect remove the bound entry in ek_shop_has_product_lang.
     *
     * Note: in this multishop version of ekom,
     * we don't touch the association between the product and the card of the general catalog.
     * Use the GeneralProductCatalogLayer instead for that.
     */
    public static function removeProduct($productId, $shopId=null)
    {
         $shopId = E::getShopId($shopId);
        $productId = (int)$productId;
        QuickPdo::delete("ek_shop_has_product", [
            ['shop_id', ' =', $shopId],
            ['product_id', ' =', $productId],
        ]);
    }
}