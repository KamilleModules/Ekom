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
use Module\Ekom\Helper\ConditionRulesHelper;
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\SokoForm\Controls\SokoCouponRulesFreeHtmlControl;
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


    public static function decrementCouponById(int $couponId)
    {
        QuickPdo::freeStmt("
update ek_coupon set quantity = quantity - 1 
where id=$couponId and quantity > 0        
        ");
    }


    public static function getActionTypesList()
    {
        return [
            "f" => "Réduction fixe",
            "p" => "Réduction proportionnelle",
            "o" => "Offert",
        ];
    }


    public static function getListItems()
    {
        return QuickPdo::fetchAll('
select id, label
from ek_coupon
', [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getCouponInfoByCode($code)
    {
        return QuickPdo::fetch("select 
c.*,
s.name as seller_name,
s.label as seller_label
from ek_coupon c 
left join ek_seller s on s.id=c.seller_id
where c.code=:code", [
            "code" => $code,
        ]);
    }


    /**
     * @return array
     * @see EkomModels::couponInfoModel()
     */
    public static function getCouponInfoById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("
select 
c.*,
s.name as seller_name,
s.label as seller_label
from ek_coupon c 
left join ek_seller s on s.id=c.seller_id
where c.id=$id
");
    }


    /**
     * @return array of couponInfo
     * @see EkomModels::couponInfoModel()
     */
    public static function getCouponInfoByIds(array $couponIds)
    {
        if ($couponIds) {
            $couponIds = array_map('intval', $couponIds);
            $sIds = implode(', ', $couponIds);
            return QuickPdo::fetchAll("
select            
c.*,
s.name as seller_name,
s.label as seller_label
from ek_coupon c 
left join ek_seller s on s.id=c.seller_id
where c.id in ($sIds)
");
        }
        return [];
    }

    public static function getCouponCodeById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select code from ek_coupon where id=$id", [], \PDO::FETCH_COLUMN);
    }

    /**
     *
     * @deprecated
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
//    public static function applyCoupons(array $couponInfoItems, $price, array &$cartModel, array &$couponsDetails)
//    {
//        $discountPrice = $price;
//        foreach ($couponInfoItems as $couponInfoItem) {
//            $error = null;
//            if (true === self::couponIsValid($couponInfoItem, $cartModel, $error)) {
//                $discountPrice = self::applyCoupon($couponInfoItem, $discountPrice, $cartModel, $couponsDetails);
//            }
//        }
//        return $discountPrice;
//    }


    /**
     * Apply coupon ids to the cart.
     * The couponDetails array is filled accordingly.
     * @see EkomModels::couponDetailsModel()
     *
     */
    public static function applyCouponsByIds(array $couponIds, $price, array &$cartModel, array &$couponsDetails)
    {
        $couponInfoItems = self::getCouponInfoByIds($couponIds);
        $couponsTotal = 0;
        $discountPrice = $price;
        foreach ($couponInfoItems as $couponInfoItem) {
            $error = null;
            /**
             * We need to recheck that coupons are valid every time,
             * that's because the content of the cart can change on every page...
             */
            if (true === self::couponIsValid($couponInfoItem, $cartModel, $error)) {
                $amount = self::applyCoupon($couponInfoItem, $discountPrice, $cartModel, $couponsDetails);
                $couponsTotal += $amount;
            }
        }
        return $couponsTotal;
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
     *              - max_nb_per_user_reached: the user already has reached the limit given by the administrator for this coupon.
     *
     *              Then most of the errors codes have the following syntax:
     *
     *              - mismatch:$errorType(:$errorInsiqhtValue)?
     *                      Often, the errorInsight value is just the value set as the condition by the administrator.
     *
     *
     *              Those indicate a condition mismatch.
     *
     *              The following error codes can be thrown:
     *
     *              - mismatch:seller:$condSellerLabel
     *              - mismatch:user:$condUserId
     *              - mismatch:date_start:$condDateStart
     *              - mismatch:date_end:$condDateEnd
     *              - mismatch:minimum_amount:$condMinimumAmount
     *              - mismatch:country_id:$sIds
     *                          $sIds being a csv of cond country ids
     *              - mismatch:user_group_id:$sIds
     *                          $sIds being a csv of cond user group ids
     *              - mismatch:cumulable:$sIds
     *                          $sIds being a csv of cond cumulable coupons ids
     *              - mismatch:condition_rules,
     *                      this indicates that a condition rule has failed.
     *                      There is no insight, but you can switch to debug mode if you want:
     *                      see more in class-modules/Ekom/Helper/ConditionRulesHelper.php
     *
     *
     *
     * @return bool: whether or not the coupon is valid
     *
     */
    public static function couponIsValid(array $couponInfo, array $cartModel, &$error = null)
    {

        if ('1' === $couponInfo['active']) {
            //--------------------------------------------
            // CHECKING SELLER
            //--------------------------------------------
            $sellerId = $couponInfo['seller_id'];
            if (null !== $sellerId) {
                /**
                 * If a seller is designated as a coupon target, the coupon applies only if there is at least one item
                 * from that seller in the cart.
                 */
                $sellerName = $couponInfo['seller_name'];
                if (false === CartHelper::cartContainsFromSeller($cartModel, $sellerName)) {
                    $sellerLabel = $couponInfo['seller_label'];
                    $error = 'mismatch:seller:' . $sellerLabel;
                    return false;
                }
            }

            //--------------------------------------------
            // CHECKING OTHER CONDITIONS
            //--------------------------------------------
            $userContext = E::getUserContext();


            $condUserId = $couponInfo['cond_user_id'];
            if (null !== $condUserId) {
                $userId = E::getUserId(null);
                if ($userId !== $condUserId) {
                    $error = "mismatch:user:$condUserId";
                }
            }


            $condDateStart = $couponInfo['cond_date_start'];
            if (null !== $condDateStart) {
                $curDatetime = date('Y-m-d H:i:s');
                if ($condDateStart > $curDatetime) {
                    $error = "mismatch:date_start:$condDateStart";
                }
            }

            $condDateEnd = $couponInfo['cond_date_end'];
            if (null !== $condDateEnd) {
                $curDatetime = date('Y-m-d H:i:s');
                if ($condDateEnd < $curDatetime) {
                    $error = "mismatch:date_end:$condDateEnd";
                }
            }


            $condMinimumAmount = $couponInfo['cond_minimum_amount'];
            if (null !== $condMinimumAmount) {
                $currentAmount = $cartModel['cart_total_tax_excluded'];
                if ($currentAmount < $condMinimumAmount) {
                    $error = "mismatch:minimum_amount:$condMinimumAmount";
                }
            }


            $condCountryIds = StringTool::unserializeAsArray($couponInfo['cond_country_id']);
            if ($condCountryIds) {

                $userShippingCountry = $userContext['shipping_country'];
                $sIds = implode(',', $condCountryIds);

                $row = QuickPdo::fetch("
select id 
from ek_country  
where 
id in ($sIds)
and iso_code = :iso_code
      
                ", [
                    "iso_code" => $userShippingCountry,
                ]);

                if (false === $row) {
                    $error = "mismatch:country_id:$sIds";
                }
            }


            $condUserGroupIds = StringTool::unserializeAsArray($couponInfo['cond_user_group_id']);
            if ($condUserGroupIds) {
                $userGroupId = (string)$userContext['user_group_id'];
                $sIds = implode(',', $condUserGroupIds);
                if (false === in_array($userGroupId, $condUserGroupIds, true)) {
                    $error = "mismatch:user_group_id:$sIds";
                }
            }


            $condCumulableWithCouponIds = StringTool::unserializeAsArray($couponInfo['cond_cumulable_with_coupon_id']);
            if ($condCumulableWithCouponIds) {
                $userCouponIds = $cartModel['coupons'];
                /**
                 * If the user has no coupons yet,
                 * then we allow without conditions
                 */
                if (!empty($userCouponIds)) {

                    $isCumulable = false;
                    foreach ($userCouponIds as $userCouponId) {
                        $userCouponId = (string)$userCouponId;
                        if (in_array($userCouponId, $condCumulableWithCouponIds, true)) {
                            $isCumulable = true;
                            break;
                        }
                    }

                    if (false === $isCumulable) {
                        $sIds = implode(',', $condCumulableWithCouponIds);
                        $error = "mismatch:cumulable:$sIds";
                    }
                }
            }


            //--------------------------------------------
            // CONDITION RULES
            //--------------------------------------------
            $condRules = $couponInfo['cond_rules'];
            if (null !== $condRules) {
                $aCondRules = ConditionRulesHelper::uncompile($condRules);

                if ($aCondRules) {
                    $isValid = true;
                    foreach ($aCondRules as $conditionRule) {

                        $res = ConditionRulesHelper::evaluateRule($conditionRule, $cartModel);
                        if (false === $res) {
                            $isValid = false;
                            break;
                        }
                    }
                    if (false === $isValid) {
                        $error = "mismatch:condition_rules";
                    }
                }
            }


            if (null !== $error) {
                return false;
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
     * This method adds an item to the coupon details array, containing the info about the coupons.
     * @see EkomModels::couponDetailsModel()
     *
     */
    private static function applyCoupon(array $couponInfoItem, $price, array $currentCartModel, array &$couponsDetails)
    {
        $amount = 0;
        if ("1" === $couponInfoItem['active']) {


            $willBeAdded = true;

            $actionType = $couponInfoItem['action_type'];
            $actionValue = $couponInfoItem['action_value'];

            switch ($actionType) {
                case 'f':
                    $amount = E::trimPrice($actionValue);
                    break;
                case 'p':
                    $newPrice = $price - ($actionValue * $price) / 100;
                    if ($newPrice < 0) {
                        $newPrice = 0;
                    }
                    $amount = E::trimPrice($price - $newPrice);
                    break;
                /**
                 * This is offered, but depends on the target.
                 * For now, it's only available with the following targets:
                 *
                 *
                 * - shipping_cost
                 */
                case 'o':

                    if ('shipping_cost' === $couponInfoItem['target']) {
                        $amount = $currentCartModel['shipping_cost_tax_included'];
                    } else {
                        $willBeAdded = false;
                    }

                    break;
                default:
                    XLog::error("[Ekom module] - CouponLayer: unknown action type: " . $actionType);
                    break;
            }


            if (true === $willBeAdded) {
                $couponsDetails[] = [
                    "seller_id" => $couponInfoItem['seller_id'],
                    "seller_name" => $couponInfoItem['seller_name'],
                    "target" => $couponInfoItem['target'],
                    "code" => $couponInfoItem['code'],
                    "label" => $couponInfoItem['label'],
                    "amount" => $amount,
                    "amount_formatted" => E::price($amount),
                    "details" => [],
                ];
            }
        }
        return $amount;
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
    public static function applyCouponDeprecated(array $couponInfoItem, $price, array $currentCartModel, array &$couponsDetails)
    {
        $discountPrice = $price;
        if ("1" === $couponInfoItem['active']) {
            $discountInfo = [
                "type" => $couponInfoItem['procedure_type'],
                "operand" => $couponInfoItem['procedure_operand'],
            ];


            $discountPrice = DiscountLayer::applyDiscountInfoToPrice($discountInfo, $discountPrice);

            $details = [];

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

