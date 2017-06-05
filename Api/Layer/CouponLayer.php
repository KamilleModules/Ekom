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
    public function tryAddCouponByCode($code, array &$couponBag = null, &$errors = null)
    {
        if (null === $couponBag) {
            $couponBag = EkomApi::inst()->cartLayer()->getCouponBag();
        }

        // does the coupon conditions validates?
        $errors = [];
        $ok = $this->testCouponConditionsByCode($code, $errors);


        // if so, can the coupon be added to the bag?
        if (true === $ok) {
            if (false !== ($id = $this->getCouponIdByCode($code))) {
                if (true === $this->canBeAddedToBag($id, $couponBag, $errors)) {
                    $couponBag[] = $id;
                }
            } else {
                $errors[] = "invalid coupon code: $code";
                return false;
            }
        }
        return $ok;
    }

    private function canBeAddedToBag($id, array $couponBag, array &$errors = [])
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
     * @return false|array of $target => discountOperationDetails,
     *                  Each discountOperationDetail is an array with the following structure:
     *
     *                      - couponCode: string
     *                      - couponLabel: string
     *                      - discountLabel: string
     *                      - old: float, just a reference to the price BEFORE the discount was applied
     *                      - newPrice: string, the formatted price (AFTER the discount was applied)
     *
     *
     */
    public function applyCouponBag(array $couponBag, array $targets, array &$validCoupons = [])
    {

        $details = [
            'linesTotalWithTax' => [],
        ];
        try {

            if (array_key_exists('linesTotalWithTax', $targets)) {

                $linesTotalWithTax = $targets['linesTotalWithTax'];
                foreach ($couponBag as $id) {

                    $info = $this->getCouponInfo($id, true);
                    if (true === $this->testCouponConditionsByCode($info['code'])) {
                        $validCoupons[] = $id;

                        $discounts = $info['discounts'];
                        foreach ($discounts as $target => $discount) {
                            switch ($target) {
                                case 'linesTotalWithTax':
                                    $old = $linesTotalWithTax;
                                    $linesTotalWithTax = $this->applyCouponDiscount($linesTotalWithTax, $discount);
                                    $details['linesTotalWithTax'][] = [
                                        'couponCode' => $info['code'],
                                        'couponLabel' => $info['label'],
                                        'discountLabel' => $discount['label'],
                                        'old' => $old,
                                        'newPrice' => E::price($linesTotalWithTax),
                                    ];
                                    break;
                                default:
                                    throw new \Exception("unknown target: $target");
                                    break;
                            }
                        }
                    }
                }
            } else {
                throw new \Exception("target undefined: linesTotalWithTax");
            }
        } catch (\Exception $e) {
            XLog::error("[Ekom module] - CouponLayer.applyCouponBag: $e");
            return false;
        }
        return $details;
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
                $price -= $operand;
                break;
            case 'relativePercent':
                $price -= $price * $operand / 100;
                break;
            default:
                throw new \Exception("Unknown procedure_type: $type");
                break;
        }
        return $price;
    }

}

