<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductCodeLayer
{

    public function addCodeToProduct($code, $productId, $shopId = null, $replace = true)
    {
        $shopId = E::getShopId($shopId);
        $productId = (int)$productId;

        $codes = $this->getCodesFromProduct($productId, $shopId);
        if (true === $replace) {
            if (!in_array($code, $codes)) {
                $codes[] = $code;
            }
        } else {
            $codes[] = $code;
        }
        return QuickPdo::update("ek_shop_has_product", [
            'codes' => implode(',', $codes),
        ], [
            ['shop_id', '=', $shopId],
            ['product_id', '=', $productId],
        ]);
    }

    public function removeCodeFromProduct($code, $productId, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $productId = (int)$productId;

        $codes = $this->getCodesFromProduct($productId, $shopId);
        if (false !== ($index = array_search($code, $codes))) {
            unset($codes[$index]);
        }

        $sCode = '';
        if ($codes) {
            $sCode = implode(',', $codes);
        }

        return QuickPdo::update("ek_shop_has_product", [
            'codes' => $sCode,
        ], [
            ['shop_id', '=', $shopId],
            ['product_id', '=', $productId],
        ]);
    }

    public function getCodesFromProduct($productId, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $productId = (int)$productId;

        $row = QuickPdo::fetch("
select 
codes 
from
ek_shop_has_product 
where  
shop_id=$shopId        
and product_id=$productId
        ");
        if (false !== $row) {
            if ('' !== $row['codes']) {
                $codes = $row['codes'];
                return explode(",", $codes);
            }
        }
        return [];
    }

    //--------------------------------------------
    //
    //--------------------------------------------

}