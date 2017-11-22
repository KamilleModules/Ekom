<?php


namespace Module\Ekom\Api\Layer;


use Bat\FileSystemTool;
use Bat\StringTool;
use Bat\UriTool;
use Core\Services\A;
use Core\Services\Hooks;
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
 * - code: coupon code
 * - label: coupon label
 * - savingRaw: the unformatted amount of saving for the ensemble of the discounts for this coupon
 * - saving: the formatted version of savingRaw
 * - details: array, free form. Example:
 *          - sellerDetails:
 *                  - seller1:
 *                      - amount: 2.51
 *                      - label: 25.15 % de 1336
 *                  - seller2:
 *                      - amount: 7.49
 *                      - label: 74.85 % de 1336
 *
 *
 */
class CouponLayer
{

    public static function getCouponInfoItemsByIds(array $ids, $langId = null)
    {


        if ($ids) {
            $langId = E::getLangId($langId);
            $ids = array_map('intval', $ids);
            $sIds = implode(', ', $ids);
            return QuickPdo::fetchAll("
select
c.id, 
c.code, 
c.active, 
c.procedure_type, 
c.procedure_operand, 
c.target, 
c.shop_id, 
cl.label
from ek_coupon c 
inner join ek_coupon_lang cl on cl.coupon_id=c.id
where c.id in ($sIds)
and cl.lang_id=$langId
");
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
     * Apply coupon(s) to the given cartModel (as defined at the top of the CartLayer class).
     *
     *
     * @param array $couponInfoItems , array of couponInfo (defined at the top of the CouponLayer class)
     * @param $price
     * @param array $cartModel (in case the coupon needs to execute some heuristics)
     *                  the cartModel being built, which structure might be slightly different depending
     *                  on the target (i.e. if target=cartTotalWithShipping, the model will contain a few more
     *                  properties than if target=linesTotal).
     * @param array $couponsDetails , for each coupon applied,
     *                              a couponDetailsItem array (defined at the top of the CouponLayer class)
     *                              is attached to $couponsDetails
     * @return number, the discount price
     */
    public static function applyCoupons(array $couponInfoItems, $price, array &$cartModel, array &$couponsDetails)
    {
        $discountPrice = $price;
        foreach ($couponInfoItems as $couponInfoItem) {
            $discountPrice = self::applyCoupon($couponInfoItem, $discountPrice, $cartModel, $couponsDetails);
        }
        return $discountPrice;
    }


    /**
     * Apply the given coupon to the given basePrice,
     * return the discount price, and add a couponDetailsItem (see at the top of this document)
     * to the $couponsDetails parameter (only if the coupon was actually applied).
     *
     *
     * @param array $couponInfoItem
     * @param $price
     * @param array $currentCartModel
     * @param array $couponsDetails
     * @return float
     */
    public static function applyCoupon(array $couponInfoItem, $price, array $currentCartModel, array &$couponsDetails)
    {
        $discountPrice = $price;
        if ("1" === $couponInfoItem['active']) {
            $discountInfo = [
                "type" => $couponInfoItem['procedure_type'],
                "operand" => $couponInfoItem['procedure_operand'],
            ];


            $discountPrice = DiscountLayer::applyDiscountInfoToPrice($discountInfo, $discountPrice);

            $details = [];
            Hooks::call("Ekom_Coupon_collectCouponDetails", $details, $price, $discountPrice, $currentCartModel);


            $saving = E::trimPrice($price - $discountPrice);
            $couponsDetails[] = [
                "target" => $couponInfoItem['target'],
                "details" => $details,
                "code" => $couponInfoItem['code'],
                "label" => $couponInfoItem['label'],
                "savingRaw" => $saving,
                "saving" => E::price($saving),
            ];

        }
        return $discountPrice;
    }

}

