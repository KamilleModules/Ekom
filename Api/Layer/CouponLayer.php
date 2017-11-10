<?php


namespace Module\Ekom\Api\Layer;


use Bat\FileSystemTool;
use Bat\StringTool;
use Bat\UriTool;
use Core\Services\A;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * couponInfo
 * ================
 * - code: string, the code of the coupon
 * - active: 1|0, whether or not the coupon code is considered active when added to the coupon bag
 * - procedure_type: string (see discountItem at the top of DiscountLayer class)
 * - procedure_operand: string (see discountItem at the top of DiscountLayer class)
 * - target: string, the target of the coupon (see database.md for more info)
 * - label: string, the coupon label
 *
 *
 *
 * couponDetailsItem
 * ===============
 * - target, the target being used
 * - code: coupon code
 * - label: coupon label
 * - savingRaw: the unformatted amount of saving for the ensemble of the discounts for this coupon
 * - saving: the formatted version of savingRaw
 *
 *
 */
class CouponLayer
{

    public static function getCouponInfoItemsByIds(array $ids)
    {
        if ($ids) {

            $ids = array_map('intval', $ids);
            $sIds = implode(', ', $ids);
            return QuickPdo::fetchAll("select * from ek_coupon where id in ($sIds)");
        }
        return [];
    }

    public static function getCouponInfoByCode($code)
    {
        return QuickPdo::fetch("select * from ek_coupon where code=:code", [
            "code" => $code,
        ]);
    }

    public static function getCouponInfoById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select * from ek_coupon where id=$id");
    }

    /**
     *
     * Apply coupon(s) with target $target on the given $price and return the discount price.
     *
     * @param array $couponInfoItems , array of couponInfo (defined at the top of the CouponLayer class)
     * @param $target
     * @param $price
     * @param array $currentCartModel (in case the coupon needs to execute some heuristics)
     *                  the cartModel being built, which structure might be slightly different depending
     *                  on the target (i.e. if target=cartTotalWithShipping, the model will contain a few more
     *                  properties than if target=linesTotal).
     * @param array $couponsDetails , for each coupon applied,
     *                              a couponDetailsItem array (defined at the top of the CouponLayer class)
     *                              is attached to $couponsDetails
     * @return number, the discount price
     */
    public static function applyCouponsByTarget(array $couponInfoItems, $target, $price, array $currentCartModel, array &$couponsDetails)
    {
        $discountPrice = $price;
        foreach ($couponInfoItems as $couponInfoItem) {
            if ($target === $couponInfoItem['target']) {
                $discountPrice = self::applyCoupon($couponInfoItem, $discountPrice, $currentCartModel, $couponsDetails);
            }
        }
        return $discountPrice;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function applyCoupon(array $couponInfoItem, $price, array $currentCartModel, array &$couponsDetails)
    {
        $discountPrice = $price;
        if ("1" === $couponInfoItem['active']) {
            $discountInfo = [
                "type" => $couponInfoItem['procedure_type'],
                "operand" => $couponInfoItem['procedure_operand'],
            ];
            $discountPrice = DiscountLayer::applyDiscountInfoToPrice($discountInfo, $discountPrice);
            $couponsDetails[] = [
                "target" => $couponInfoItem['target'],
                "code" => $couponInfoItem['code'],
                "label" => $couponInfoItem['label'],
                "savingRaw" => $discountPrice,
                "saving" => E::price($discountPrice),
            ];

        }
        return $discountPrice;
    }

}

