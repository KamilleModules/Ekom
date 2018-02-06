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
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\E;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\QuickPdo;


/**
 *
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

    public static function getCouponCodeById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select code from ek_coupon where id=$id", [], \PDO::FETCH_COLUMN);
    }

    /**
     *
     * Apply coupon(s) (or not) to the given cartModel (as defined at the top of the CartLayer class).
     * The coupon only applies if there is a viable target in the cart.
     *
     * We use the target property of the couponInfoItem to decide whether or not the target can be found in
     * the cart.
     * @see EkomModels::couponDetailsItem()
     *
     * If target is empty
     *
     *
     *
     *
     *
     * @param array $couponInfoItems , array of couponInfo
     * @see EkomModels::couponInfo()
     * @param $price
     * @param array $cartModel (in case the coupon needs to execute some heuristics)
     *                  the cartModel being built, which structure might be slightly different depending
     *                  on the target (i.e. if target=cartTotalWithShipping, the model will contain a few more
     *                  properties than if target=linesTotal).
     * @param array $couponsDetails , for each coupon applied,
     *                              a couponDetailsItem array -- @see EkomModels::couponDetailsItem()
     *                              is attached to $couponsDetails
     * @return number, the discount price
     * @throws \Exception
     */
    public static function applyCoupons(array $couponInfoItems, $price, array &$cartModel, array &$couponsDetails)
    {
        $discountPrice = $price;
        foreach ($couponInfoItems as $couponInfoItem) {
            $error = null;
            if (true === self::couponIsValid($couponInfoItem, $cartModel, $error)) {
                $discountPrice = self::applyCoupon($couponInfoItem, $discountPrice, $cartModel, $couponsDetails);
            }
        }
        return $discountPrice;
    }


    /**
     *
     * Check whether or not a coupon is valid.
     * This is meant to be a low level tool.
     *
     * @param array $couponInfo , the <couponInfo>
     * @see EkomModels::couponInfo()
     * @param array $cartModel
     * @param null|string $error , an error code indicating what type of failure occurred (in case the coupon couldn't
     *              be added). Possible error codes are:
     *              - inactive: the coupon is inactive
     *              - mismatch: the coupon cannot apply to the current cart
     *                          (for instance because you don't have certain products in your cart)
     * @return bool: whether or not the coupon is valid
     *
     */
    public static function couponIsValid(array $couponInfo, array $cartModel, &$error = null)
    {
        if ('1' === $couponInfo['active']) {
            //--------------------------------------------
            // CHECKING SELLER TARGET
            //--------------------------------------------
            $seller = self::getSellerByTarget($couponInfo['target']);
            /**
             * If a seller is designated as a coupon target, the coupon applies only if there is at least one item
             * from that seller in the cart.
             */
            if (null !== $seller) {
                if (false === CartHelper::cartContainsFromSeller($cartModel, $seller)) {
                    $error = 'mismatch';
                    return false;
                }
            }
            return true;
        } else {
            $error = 'inactive';
            return false;
        }
    }

    /**
     * Check whether or not a coupon is valid.
     * This is meant to be a method for a web interface.
     *
     * @param string $code , the coupon code
     * @param array $cartModel
     * @param null|string $error , an error code indicating what type of failure occurred (in case the coupon couldn't
     *              be added). Possible error codes are:
     *              - notFound: the coupon does not exist in the database
     *              - ...plus, all codes returned by couponIsValid method
     *
     * @param array|null $couponInfo , the couponInfo is filled if the coupon is found
     * @see EkomModels::couponInfo()
     * @return bool: whether or not the coupon is valid
     */
    public static function couponIsValidByCode($code, array $cartModel, &$error = null, array &$couponInfo = null)
    {
        $_couponInfo = CouponLayer::getCouponInfoByCode($code);
        if (false !== $_couponInfo) {
            $couponInfo = $_couponInfo;
            return self::couponIsValid($_couponInfo, $cartModel, $error);
        } else {
            $error = "notFound";
        }
        return false;
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
     * @throws \Exception
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


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     *
     * Read the target string -- from @see EkomModels::couponDetailsItem()
     * and returns the target seller name, or null if no seller was designed as a target.
     *
     *
     *
     *
     * @param $target
     * @return null|string
     */
    private static function getSellerByTarget($target)
    {
        if (0 === strpos($target, "seller:")) {
            $p = explode(':', $target, 2);
            return trim($p[1]);
        }
        return null;
    }
}

