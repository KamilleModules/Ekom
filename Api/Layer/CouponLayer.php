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
    public function tryAddCouponByCode($code, &$errors = null)
    {

        $couponBag = EkomApi::inst()->cartLayer()->getCouponBag();

        // does the coupon conditions validates?
        $errors = [];
        $ok = $this->testCouponConditionsByCode($code, $errors);


        // if so, can the coupon be added to the bag?
        if (true === $ok) {
            if (false !== ($id = $this->getCouponIdByCode($code))) {
                if (true === $this->mergeToBag($id, $couponBag, $errors)) {


                    EkomApi::inst()->cartLayer()->setCouponBag($couponBag);
                }
            } else {
                $errors[] = "invalid coupon code: $code";
                return false;
            }
        }
        return $ok;
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
        $info = $this->getCouponInfo($id, false);
        $code = $info['code'];
        if ('1' === $info['active']) {
            $mode = $info['mode'];
            if (0 === count($couponBag)) {
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
    }

    public function getCouponIdByCode($code)
    {
        return A::cache()->get("Module.Ekom.Api.Layer.CouponLayer.getCouponIdByCode.$code", function () use ($code) {
            return EkomApi::inst()->coupon()->readColumn("id", [
                ['code', "=", $code],
            ]);
        }, [
            "coupon.delete.*",
            "coupon.update.*",
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
     *          expected targets names are:
     *          - linesTotalWithTax
     *          - linesTotalWithTaxAndShipping
     *
     *
     *
     *
     * @param array $validCoupons , the coupons in the bag, minus those discarded by failing condition
     *
     *
     * @return false|array with the following structure:
     *
     *              - cartTotal, the formatted price (linesTotalWithTax with all coupon's discounts with target linesTotalWithTax
     *                          applied to it, see ekom order model II for more info).
     *              - totalSaving, the formatted amount of saving for the linesTotalWithTax target
     *              - coupons: array of couponCode => couponDetail, each couponDetail is an array with the following structure:
     *
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
    public function applyCouponBag(array $couponBag, array $targets, array &$validCoupons = [])
    {
        try {

            if (array_key_exists('linesTotalWithTax', $targets)) {

                $ret = [];

                $linesTotalWithTax = $targets['linesTotalWithTax'];
                $cartTemp = $linesTotalWithTax;
                $coupons = [];
                foreach ($couponBag as $id) {

                    $info = $this->getCouponInfo($id, true);
                    $coupon = [
                        'label' => $info['label'],
                    ];

                    if (true === $this->testCouponConditionsByCode($info['code'])) {
                        $validCoupons[] = $id;

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

                            $coupon['discounts'] = $_discounts;
                            $coupon['saving'] = E::price(-($temp - $linesTotalWithTax));
                        }

                        $coupons[$info['code']] = $coupon;

                    }
                }

                $ret['cartTotal'] = E::price($linesTotalWithTax);
                $ret['totalSaving'] = E::price(-($cartTemp - $linesTotalWithTax));
                $ret['coupons'] = $coupons;


                // updating the couponBag so that only valid coupons remain
                EkomApi::inst()->cartLayer()->setCouponBag($validCoupons);

                return $ret;

            } else {
                throw new \Exception("target undefined: linesTotalWithTax");
            }
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

