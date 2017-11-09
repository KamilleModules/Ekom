<?php


namespace Module\Ekom\Api\Layer;


use Bat\ArrayTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class BundleLayer
{


    public function addBundleToCart($bundleId, array $removedProductIds = [])
    {

        $productId2Qty = [];

        $productInfos = $this->getProductInfoByBundleId($bundleId);
        foreach ($productInfos as $k => $info) {
            $pId = $info['product_id'];
            if (in_array($pId, $removedProductIds)) {
                unset($productInfos[$k]);
            }
        }

        foreach ($productInfos as $info) {
            $productId2Qty[$info['product_id']] = $info['quantity'];
        }


        EkomApi::inst()->cartLayer()->addItems($productId2Qty);
        return $productId2Qty;
    }

    /**
     * array $removedProductIds, array with the following structure:
     *              bundleId => removedProductIds,
     *                  removedProductIds is an array of productId.
     *
     */
    public function getBundleModelByProductId($productId, array $removedProductIds = [], $shopId = null)
    {
        $productId = (int)$productId;
        $shopId = E::getShopId($shopId);


        $sIds = '';
        ksort($removedProductIds);
        foreach ($removedProductIds as $bId => $pIds) {
            sort($pIds);
            $sIds .= $bId . '-' . implode('.', $pIds);
        }


        return A::cache()->get("Ekom.BundleLayer.getBundleModelByProductId.$shopId.$productId.$sIds", function () use ($productId, $shopId, $removedProductIds) {

            $ret = [];
            $totalsWithTax = [];
            $bundleIds = $this->getBundleIdsByProductId($productId, $shopId);


            if (count($bundleIds) > 0) {


                $rows = QuickPdo::fetchAll("
select 
h.product_bundle_id,
h.product_id,
h.quantity

 
from ek_product_bundle_has_product h 
inner join ek_product_bundle b on b.id=h.product_bundle_id
 
where h.product_bundle_id in(" . implode(', ', $bundleIds) . ") 
and b.shop_id=$shopId
and b.active=1
        
        ");

                $requiredProps = [
                    "label",
                    "ref",
                    "priceSale",
                    "priceBase",
                    "discountHasDiscount",
                    "uriCard",
                ];

                foreach ($rows as $row) {


                    $bid = $row['product_bundle_id'];
                    $id = (int)$row['product_id'];

                    $isVisible = true;

                    if (array_key_exists($bid, $removedProductIds) && in_array($id, $removedProductIds[$bid])) {
                        $isVisible = false;
                    }

                    if (false === array_key_exists($bid, $ret)) {
                        $ret[$bid] = [];
                    }
                    $model = ProductBoxLayer::getProductBoxByProductId($id);

                    $img = $model['images'][$model['defaultImage']]['small'];
                    $qty = $row['quantity'];

                    $bundleModel = ArrayTool::superimpose($model, array_flip($requiredProps));
                    $bundleModel['quantity'] = $qty;
                    $bundleModel['image'] = $img;

                    $bundleModel['total'] = "";
                    $bundleModel['isCurrentItem'] = ($productId === (int)$row['product_id']) ? true : false;
                    $bundleModel['product_id'] = $row['product_id'];
                    $bundleModel['identifier'] = $bid . '-' . $row['product_id'];
                    /**
                     * Note: as for now this is_visible property is
                     * only for dynamic changes (when the user unchecks a by default selected checkbox)
                     */
                    $bundleModel['is_visible'] = $isVisible;

                    $ret[$bid]['items'][] = $bundleModel;
                    if (true === $isVisible) {
                        $totalsWithTax[$bid][] = $model['priceSaleRaw'] * $qty;
                    }
                }


                //--------------------------------------------
                // applying totals
                //--------------------------------------------
                foreach ($rows as $row) {
                    $totalWithTax = 0;
                    $bid = $row['product_bundle_id'];
                    if (array_key_exists($bid, $totalsWithTax)) {
                        $totalWithTax = array_sum($totalsWithTax[$bid]);
                    }
                    $ret[$bid]['totalSalePriceWithTax'] = E::price($totalWithTax);
                }

            }

            return $ret;
        }, [
            "ek_product_bundle_has_product",
            "ek_product_bundle",

            // box model
            "ek_shop_has_product_card_lang",
            "ek_shop_has_product_card",
            "ek_product_card_lang",
            "ek_product_card",
            "ek_shop",
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ek_product.delete",
            "ek_product.update",
            "ekomApi.image.product",
            "ekomApi.image.productCard",
        ]);

    }

    public function getBundleIdsByProductId($productId, $shopId = null)
    {
        EkomApi::inst()->initWebContext();
        $productId = (int)$productId;
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;


        return A::cache()->get("Ekom.BundleLayer.getBundleIdsByProductId.$shopId.$productId", function () use ($shopId, $productId) {

            return QuickPdo::fetchAll("
select h.product_bundle_id
 
from ek_product_bundle_has_product h 
inner join ek_product_bundle b on b.id=h.product_bundle_id
 
where h.product_id=$productId 
and b.shop_id=$shopId
and b.active=1
        
        ", [], \PDO::FETCH_COLUMN);

        }, [
            "ek_product_bundle_has_product",
            "ek_product_bundle",
        ]);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function getProductInfoByBundleId($bundleId)
    {

        return A::cache()->get("Ekom.BundleLayer.getProductIdsByBundleId.$bundleId", function () use ($bundleId) {

            return QuickPdo::fetchAll("
select 
h.product_id,
h.quantity
 
from ek_product_bundle_has_product h 
inner join ek_product_bundle b on b.id=h.product_bundle_id
 
where b.id=$bundleId 
and b.active=1
        
        ");

        }, [
            "ek_product_bundle_has_product",
            "ek_product_bundle",
        ]);
    }

}