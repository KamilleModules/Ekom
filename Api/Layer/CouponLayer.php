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

class CouponLayer
{
    public function testCouponConditionsByCode($code, &$errors = [])
    {

        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $code = FileSystemTool::noEscalating($code);
        $f = ApplicationParameters::get("app_dir") . "/data/Ekom/coupon-conditions/$shopId/$code.php";
        if (file_exists($f)) {
            $func = null;
            include $f;

            if (is_callable($func)) {
                $ret = (bool)call_user_func_array($func, [&$errors]);
                if (true === $ret) { // just in case the dev set errors while returning true, cleaning up a bit
                    $errors = [];
                }
                return $ret;
            }
        } else {
            return true;
        }
    }

    /**
     *
     * Try to add the coupon to the given couponBag.
     *
     *
     * @param $code
     * @param array|null &$couponBag
     * @param string|null &$errors , an array of errors indicating the reason why the coupon was not added.
     *
     * @return bool, whether or not the coupon was added
     */
    public function tryAddCouponByCode($code, &$errors = null, &$mode = null)
    {

        $couponBag = EkomApi::inst()->cartLayer()->getCouponBag();

        // does the coupon conditions validates?
        $errors = [];
        $ok = $this->testCouponConditionsByCode($code, $errors);


        // if so, can the coupon be added to the bag?
        if (true === $ok) {
            if (false !== ($row = $this->getCouponInfoByCode($code))) {
                if (true === $this->mergeToBag($row['id'], $couponBag, $errors)) {
                    $mode = $row['mode'];
                    EkomApi::inst()->cartLayer()->setCouponBag($couponBag);
                    return true;
                } else {
                    return false;
                }
            } else {
                $errors[] = "invalid coupon code: $code";
                return false;
            }
        }
        return $ok;
    }

    public function removeCouponByIndex($index)
    {
        $couponBag = EkomApi::inst()->cartLayer()->getCouponBag();
        unset($couponBag[$index]);
        EkomApi::inst()->cartLayer()->setCouponBag($couponBag);
    }


    /**
     *
     * Try to add the coupon to the given couponBag.
     *
     *
     * @param $code
     * @param array|null &$couponBag
     * @param string|null &$errors , an array of errors indicating the reason why the coupon was not added.
     *
     * @return bool, whether or not the coupon was added
     */
//    public function tryAddCouponByCode($code, array &$couponBag = null, &$errors = null)
//    {
//        if (null === $couponBag) {
//            $couponBag = EkomApi::inst()->cartLayer()->getCouponBag();
//        }
//
//        // does the coupon conditions validates?
//        $errors = [];
//        $ok = $this->testCouponConditionsByCode($code, $errors);
//
//
//        // if so, can the coupon be added to the bag?
//        if (true === $ok) {
//            if (false !== ($id = $this->getCouponIdByCode($code))) {
//                if (true === $this->mergeToBag($id, $couponBag, $errors)) {
//                    $couponBag[] = $id;
//                    EkomApi::inst()->cartLayer()->setCouponBag($couponBag);
//                }
//            } else {
//                $errors[] = "invalid coupon code: $code";
//                return false;
//            }
//        }
//        return $ok;
//    }

    private function mergeToBag($id, array &$couponBag, array &$errors = [])
    {
        $id = (int)$id;
        $info = $this->getCouponInfo($id, true);
        $code = $info['code'];

        if (count($info['discounts']) > 0) {

            if ('1' === $info['active']) {
                $mode = $info['mode'];
                if (0 === count($couponBag)) {
                    $couponBag[] = $id;
                    return true;
                } else {
                    switch ($mode) {
                        case 'merge':
                            $couponBag[] = $id;
                            break;
                        case 'mergeWithSame':
                            $couponsInfo = $this->getCouponBagInfo($couponBag);
                            $diff = [];
                            foreach ($couponsInfo as $info) {
                                if ($info['code'] !== $code) {
                                    $diff[] = $info['code'];
                                }
                            }
                            if (count($diff) > 0) {
                                $errors[] = "refuted: this coupon cannot be combined with coupon(s) " . implode(', ', $diff);
                                return false;
                            }

                            $couponBag[] = $id;
                            break;
                        default: // unique
                            $priority = $info['priority'];
                            $couponsInfo = $this->getCouponBagInfo($couponBag);
                            $stronger = [];
                            foreach ($couponsInfo as $info) {
                                if ($info['priority'] > $priority) {
                                    $stronger[] = $info['code'];
                                }
                            }
                            if (count($stronger) > 0) {
                                $errors[] = "refuted: this coupon cannot replace coupon(s) " . implode(', ', $stronger);
                                return false;
                            }

                            $couponBag = [$id]; // unique REPLACE the content of the coupon bag
                            break;
                    }
                    return true;
                }
            } else {
                $errors[] = "coupon inactive: $code";
                return false;
            }
        } else {
            $errors[] = "coupon $code has no discounts bound to it, please contact the webmaster";
            return false;
        }
    }

    public function getCouponInfoByCode($code)
    {
        return A::cache()->get("Module.Ekom.Api.Layer.CouponLayer.getCouponInfoByCode.$code", function () use ($code) {
            return QuickPdo::fetch("select id, mode from ek_coupon where code=:code", [
                "code" => $code,
            ]);
        }, [
            "coupon.delete",
            "coupon.update",
        ]);
    }


    /**
     *
     * Use this method to apply a valid bag of coupons to a given order (on the cart
     * page or checkout page for instance).
     *
     *
     *
     * @param array $couponBag
     * @param array $targets , array of target name => target value,
     *          Possible target names are: (ekom order model3)
     *
     *          - linesTotal
     *          - linesTotalWithoutTax
     *          - linesTotalWithTax
     *
     *          - orderSectionSubtotal
     *          - orderSectionSubtotalWithoutTax
     *          - orderSectionSubtotalWithTax
     *
     *
     *
     *
     * @param array $validCoupons , the coupons in the bag, minus those discarded by failing condition
     *
     *
     * @return false|array with the following structure:
     *
     *              - cartTotal, the formatted cart total
     *              - rawCartTotal, float, the unformatted cart total
     *              - totalSaving, the formatted amount of saving
     *              - coupons: array of couponDetail, each couponDetail is an array with the following structure:
     *
     *                      - target, the target being used
     *                      - code: coupon code
     *                      - label: coupon label
     *                      - saving: the formatted amount of saving for the ensemble of the discounts for this coupon
     *                      - discounts: array of $target => discountDetails.
     *                                  Each discountDetail is an array with the following structure:
     *
     *                                - label: string, the discount label
     *                                - old: float, just a reference to the price BEFORE the discount was applied
     *                                - newPrice: string, the formatted price (AFTER the discount was applied)
     *
     *
     */
    public function zzzapplyCouponBag(array $couponBag, array $targets, array &$validCoupons = [])
    {
        throw new \Exception("oo");
        try {


            $ret = [];

            $linesTotal = $targets['linesTotal'];
            $linesTotalWithTax = $targets['linesTotalWithTax'];
            $linesTotalWithoutTax = $targets['linesTotalWithoutTax'];


            $cartTemp = $linesTotalWithTax;
            $coupons = [];
            foreach ($couponBag as $id) {

                $info = $this->getCouponInfo($id, true);
                $coupon = [
                    'label' => $info['label'],
                ];

                if (true === $this->testCouponConditionsByCode($info['code'])) {


                    $_discounts = [];
                    foreach ($info['discounts'] as $target => $discounts) {

                        $temp = $linesTotalWithTax;
                        foreach ($discounts as $k => $discount) {
                            $_discount = [];
                            switch ($target) {
                                case 'linesTotalWithTax':
                                    $old = $linesTotalWithTax;
                                    $linesTotalWithTax = $this->applyCouponDiscount($linesTotalWithTax, $discount);

                                    $_discount['old'] = $old;
                                    $_discount['newPrice'] = E::price($linesTotalWithTax);
                                    $_discount['label'] = $discount['label'];
                                    break;
                                default:
                                    throw new \Exception("unknown target: $target");
                                    break;
                            }
                            $_discounts[] = $_discount;
                        }


                        $coupon['target'] = $target;
                        $coupon['code'] = $info['code'];
                        $coupon['discounts'] = $_discounts;
                        $coupon['saving'] = E::price(-($temp - $linesTotalWithTax));
                    }


                    if (count($_discounts) > 0) { // some coupons are not bound to discounts yet
                        $validCoupons[] = $id;
                        $coupons[] = $coupon;
                    } else {
                        throw new \Exception("the coupon " . $info['code'] . " has no discounts bound to it!");
                    }

                }
            }

            $ret['rawCartTotal'] = $linesTotalWithTax;
            $ret['cartTotal'] = E::price($linesTotalWithTax);
            $ret['totalSaving'] = E::price(-($cartTemp - $linesTotalWithTax));
            $ret['coupons'] = $coupons;


            // updating the couponBag so that only valid coupons remain
            EkomApi::inst()->cartLayer()->setCouponBag($validCoupons);

            return $ret;


        } catch (\Exception $e) {
            XLog::error("[Ekom module] - CouponLayer.applyCouponBag: $e");
            return false;
        }
    }


    public function applyCouponBag($price, array $couponBag, array &$validCoupons = [])
    {
        try {


            $ret = [];
            $cartTemp = $linesTotalWithTax;
            $coupons = [];
            foreach ($couponBag as $id) {

                $info = $this->getCouponInfo($id, true);
                $coupon = [
                    'label' => $info['label'],
                ];

                if (true === $this->testCouponConditionsByCode($info['code'])) {


                    $_discounts = [];
                    foreach ($info['discounts'] as $target => $discounts) {

                        $temp = $linesTotalWithTax;
                        foreach ($discounts as $k => $discount) {
                            $_discount = [];
                            switch ($target) {
                                case 'linesTotalWithTax':
                                    $old = $linesTotalWithTax;
                                    $linesTotalWithTax = $this->applyCouponDiscount($linesTotalWithTax, $discount);

                                    $_discount['old'] = $old;
                                    $_discount['newPrice'] = E::price($linesTotalWithTax);
                                    $_discount['label'] = $discount['label'];
                                    break;
                                default:
                                    throw new \Exception("unknown target: $target");
                                    break;
                            }
                            $_discounts[] = $_discount;
                        }


                        $coupon['target'] = $target;
                        $coupon['code'] = $info['code'];
                        $coupon['discounts'] = $_discounts;
                        $coupon['saving'] = E::price(-($temp - $linesTotalWithTax));
                    }


                    if (count($_discounts) > 0) { // some coupons are not bound to discounts yet
                        $validCoupons[] = $id;
                        $coupons[] = $coupon;
                    } else {
                        throw new \Exception("the coupon " . $info['code'] . " has no discounts bound to it!");
                    }

                }
            }

            $ret['rawCartTotal'] = $linesTotalWithTax;
            $ret['cartTotal'] = E::price($linesTotalWithTax);
            $ret['totalSaving'] = E::price(-($cartTemp - $linesTotalWithTax));
            $ret['coupons'] = $coupons;


            // updating the couponBag so that only valid coupons remain
            EkomApi::inst()->cartLayer()->setCouponBag($validCoupons);

            return $ret;


        } catch (\Exception $e) {
            XLog::error("[Ekom module] - CouponLayer.applyCouponBag: $e");
            return false;
        }
    }


    /**
     * @param $id
     * @param bool $withCartDiscounts
     * @return array of couponInfo, which has the following structure:
     *
     *
     * - code: string, the code of the coupon
     * - active: 1|0, whether or not the coupon code is considered active when added to the coupon bag
     * - mode: unique|mergeWithSame|merge, see the $date-database.md document for more info
     * - priority: int, the priority helper for more
     * - label: string, the coupon label
     * - discounts: array of target => discount. each discount is an item with the following structure:
     *          - target: string, the target of the discount (linesTotalWithTax, linesTotalWithTaxShipping, ...), see the $date-database.md document for more info
     *                      Note, this info is also the key (it's redundant).
     *          - procedure_type: string, fixedAmount|relativeAmount|relativePercent, the type of the procedure to apply if the coupon applies
     *          - procedure_operand: string, helper value for the procedure_type. See the latest $date-database.md document for more info
     *          - label: string, the cart discount label
     *
     *
     */
    public function getCouponInfo($id, $withCartDiscounts = false)
    {

        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $langId = ApplicationRegistry::get("ekom.lang_id");
        $iWithCartDiscounts = (int)$withCartDiscounts;
        $id = (int)$id;

        return A::cache()->get("Module.Ekom.Api.Layer.CouponLayer.getCouponInfo.$shopId.$langId.$id.$iWithCartDiscounts", function () use ($id, $shopId, $langId, $withCartDiscounts) {

            $row = QuickPdo::fetch("
select 

c.code,
c.active,
c.mode,        
c.priority,        
l.label
        
from ek_coupon c 
inner join ek_coupon_lang l on l.coupon_id=c.id 


where 
c.id=$id 
and c.shop_id=$shopId
and l.lang_id=$langId                
        ");


            if (true === $withCartDiscounts) {
                $rows = QuickPdo::fetchAll("
select 
d.target,
d.procedure_type,
d.procedure_operand,
l.label

from ek_coupon_has_cart_discount h 
inner join ek_cart_discount d on d.id=h.cart_discount_id 
inner join ek_cart_discount_lang l on l.cart_discount_id=d.id

where 

h.coupon_id=$id 
and d.shop_id=$shopId
and l.lang_id=$langId
                               
                
                
                ");


                az($rows);
                $dis = [];
                foreach ($rows as $r) {
                    if (!array_key_exists($r['target'], $dis)) {
                        $dis[$r['target']] = [];
                    }
                    $dis[$r['target']][] = $r;
                }

                $row['discounts'] = $dis;
            }


            return $row;
        }, [
            "ek_coupon.delete.$id",
            "ek_coupon.update.$id",
            "ek_coupon_lang.delete.$id",
            "ek_coupon_lang.update.$id",
            "ek_shop.delete.$shopId",
            "ek_lang.delete.$langId",
        ]);

    }


    //--------------------------------------------
    //
    //--------------------------------------------


    private function getCouponBagInfo(array $couponBag, $witCartDiscounts = false)
    {
        $ret = [];
        foreach ($couponBag as $id) {
            $ret[$id] = $this->getCouponInfo($id, $witCartDiscounts);
        }
        return $ret;
    }


    private function applyCouponDiscount($price, array $discount)
    {
        $type = $discount['procedure_type'];
        $operand = $discount['procedure_operand'];

        switch ($type) {
            case 'fixedAmount':
                $price = $operand;
                break;
            case 'relativeAmount':
                $price += $operand;
                break;
            case 'relativePercent':
                $price += $price * $operand / 100;
                break;
            default:
                throw new \Exception("Unknown procedure_type: $type");
                break;
        }
        return $price;
    }

}

