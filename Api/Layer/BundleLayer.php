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
    public function getBundleModelByProductId($productId, array $removedProductIds = [])
    {
        $productId = (int)$productId;


        $sIds = '';
        ksort($removedProductIds);
        foreach ($removedProductIds as $bId => $pIds) {
            sort($pIds);
            $sIds .= $bId . '-' . implode('.', $pIds);
        }


        return A::cache()->get("Ekom.BundleLayer.getBundleModelByProductId.$productId.$sIds", function () use ($productId, $removedProductIds) {

            $ret = [];
            $totalsWithTax = [];
            $bundleIds = $this->getBundleIdsByProductId($productId);


            if (count($bundleIds) > 0) {


                $rows = QuickPdo::fetchAll("
select 
h.product_bundle_id,
h.product_id,
h.quantity

 
from ek_product_bundle_has_product h 
inner join ek_product_bundle b on b.id=h.product_bundle_id
 
where h.product_bundle_id in(" . implode(', ', $bundleIds) . ") 
and b.active=1
        
        ");


                $hasTax = false;
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


                    $img = $model['image'];
                    $qty = $row['quantity'];

//                    $bundleModel = ArrayTool::superimpose($model, array_flip($requiredProps));
                    $bundleModel = [];


                    $bundleModel['label'] = $model['label'];
                    $bundleModel['reference'] = $model['reference'];
                    $bundleModel['sale_price'] = E::price($model['sale_price']);
                    $bundleModel['base_price'] = E::price($model['base_price']);
                    $bundleModel['has_discount'] = $model['has_discount'];
                    $bundleModel['has_tax'] = $model['has_tax'];
                    $bundleModel['product_uri'] = $model['product_uri'];



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
                    if (true === $bundleModel['has_tax']) {
                        $hasTax = true;
                    }


                    $ret[$bid]['items'][] = $bundleModel;
                    if (true === $isVisible) {
                        $totalsWithTax[$bid][] = $model['sale_price'] * $qty;
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
                    $ret[$bid]['total'] = E::price($totalWithTax);
                    $ret[$bid]['has_tax'] = $hasTax;
                }
            }


            return $ret;
        });

    }

    public function getBundleIdsByProductId(int $productId)
    {


        return A::cache()->get("Ekom.BundleLayer.getBundleIdsByProductId.$productId", function () use ($productId) {

            return QuickPdo::fetchAll("
select h.product_bundle_id
 
from ek_product_bundle_has_product h 
inner join ek_product_bundle b on b.id=h.product_bundle_id
 
where h.product_id=$productId 
and b.active=1
        
        ", [], \PDO::FETCH_COLUMN);

        });
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