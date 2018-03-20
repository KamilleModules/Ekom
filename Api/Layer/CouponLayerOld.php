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

class CouponLayerOld
{


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
    public static function getCouponInfo($id, $withCartDiscounts = false)
    {


        $shopId = E::getShopId();
        $langId = E::getLangId();
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
d.type,
d.operand,
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
            "ek_coupon",
            "ek_coupon_lang",
            "ek_shop",
            "ek_lang",
        ]);
    }

    /**
     *
     * Adds a coupon to the given cart.
     * This is a shortcut method used by json/api service.
     *
     *
     *
     *
     * @param $code
     * @param $cartLayer ,
     *              a cartLayer object, this means an object containing at least
     *              the following methods:
     *                  - getCouponBag
     *                  - setCouponBag
     * @return array:
     *          - type: error|confirm|success
     *                      - error: an error occurred, it can be displayed to a gui user.
     *                                  The error texts are in the errors property.
     *                      - confirm: the confirm message --stored in the message property--
     *                                      should be displayed to the user. This can be overridden
     *                                      by setting the force flag to true.
     *                      - success: the coupon has been added. A success message is available in the
     *                                      message property.
     *          - ?errors: array, an array of error texts (only if type=error)
     *          - ?message: string, the confirm message to display (only if type=confirm|success)
     *          - ?model: array, the cart model (only if type=success)
     *
     */
    public function addCouponByCode($code, $cartLayer = null, $force = false)
    {

        if (null === $cartLayer) {
            $cartLayer = EkomApi::inst()->cartLayer();
        }
        $force = (bool)$force;
        if (false !== ($row = $this->getCouponInfoByCode($code))) {
            $mode = $row['mode'];
            if (0 === $force && ('unique' === $mode || '' === $mode)) {
                $out = [
                    'type' => 'confirm',
                    'message' => "The coupon with code $code will remove the other coupons in your cart, are you sure this is what you want?",
                ];
            } else {
                $errors = [];
                $mode = null;
                $test = $this->tryAddCouponByCode($code, $errors, $mode, $cartLayer);
                if (true === $test) {
                    $out = [
                        'type' => 'success',
                        'model' => $cartLayer->getCartModel(),
                        'message' => "The coupon with code $code has been successfully added",
                    ];

                } else {
                    $out = [
                        'type' => 'error',
                        'errors' => $errors,
                    ];
                }
            }
        } else {
            $out = [
                'type' => 'error',
                'errors' => [
                    "The coupon with code $code doesn't exist"
                ],
            ];
        }
        return $out;
    }


    public function testCouponConditionsByCode($code, array $data = [], &$errors = [])
    {


        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $code = FileSystemTool::noEscalating($code);
        $f = ApplicationParameters::get("app_dir") . "/data/Ekom/coupon-conditions/$shopId/$code.php";
        if (file_exists($f)) {
            $func = null;
            include $f;

            if (is_callable($func)) {
                $ret = (bool)call_user_func_array($func, [$data, &$errors]);
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
    public function tryAddCouponByCode($code, &$errors = null, &$mode = null, $cartLayer = null)
    {

        if (null === $cartLayer) {
            $cartLayer = EkomApi::inst()->cartLayer();
        }
        $couponBag = $cartLayer->getCouponBag();


        // does the coupon conditions validates?
        $errors = [];
        $data = [];
        $ok = $this->testCouponConditionsByCode($code, $data, $errors);


        // if so, can the coupon be added to the bag?
        if (true === $ok) {
            if (false !== ($row = $this->getCouponInfoByCode($code))) {
                if (true === $this->mergeToBag($row['id'], $couponBag, $errors)) {
                    $mode = $row['mode'];
                    $cartLayer->setCouponBag($couponBag);
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

    public function removeCouponByIndex($index, $cartLayer = null)
    {
        if (null === $cartLayer) {
            $cartLayer = EkomApi::inst()->cartLayer();
        }
        $couponBag = $cartLayer->getCouponBag();
        unset($couponBag[$index]);
        $cartLayer->setCouponBag($couponBag);
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
     * @param $price
     * @param $target : beforeShipping|afterShipping
     * @param array $couponBag
     * @param array $validCoupons
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
     */
    public function applyCouponBag($price, $priceWithTax, $target, array $couponBag, array &$validCoupons = [], array $data = [])
    {

        try {


            $ret = [];
            $originalPrice = $price;
            $originalPriceWithTax = $priceWithTax;
            $coupons = [];
            foreach ($couponBag as $id) {
                $info = $this->getCouponInfo($id, true);
                $coupon = [
                    'label' => $info['label'],
                ];

                if (true === $this->testCouponConditionsByCode($info['code'], $data)) {

                    $_discounts = [];
                    foreach ($info['discounts'] as $_target => $discounts) {
                        if ($_target === $target) {
                            $temp = $price;
                            $tempWithTax = $priceWithTax;

                            foreach ($discounts as $k => $discount) {


                                $_discount = [];
                                $old = $price;
                                $oldWithTax = $priceWithTax;
                                /**
                                 * todo: this applyCouponDiscount method probably needs the data as its input.
                                 * You might want to rebuild and create a system like for testCouponConditionsByCode,
                                 * using the filesystem?
                                 */
                                $price = $this->applyCouponDiscount($price, $discount);
                                $priceWithTax = $this->applyCouponDiscount($priceWithTax, $discount);

                                $_discount['old'] = $old;
                                $_discount['newPrice'] = E::price($price);
                                $_discount['oldWithTax'] = $oldWithTax;
                                $_discount['newPrice'] = E::price($priceWithTax);
                                $_discount['label'] = $discount['label'];

                                $_discounts[] = $_discount;
                            }


                            $coupon['target'] = $target;
                            $coupon['code'] = $info['code'];
                            $coupon['discounts'] = $_discounts;
                            $coupon['saving'] = E::price(-($temp - $price));
                            $coupon['savingWithTax'] = E::price(-($tempWithTax - $priceWithTax));
                        }
                    }

                    if (count($_discounts) > 0) { // some coupons are not bound to discounts yet
                        $validCoupons[] = $id;
                        $coupons[] = $coupon;
                    }

                }
            }

            $totalSaving = ($originalPrice - $price);
            $totalSavingWithTax = ($originalPriceWithTax - $priceWithTax);
            $ret['rawDiscountPrice'] = $price;
            $ret['discountPrice'] = E::price($price);
            $ret['rawDiscountPriceWithTax'] = $priceWithTax;
            $ret['discountPriceWithTax'] = E::price($priceWithTax);

            $ret['rawTotalSaving'] = $totalSaving;
            $ret['totalSaving'] = E::price(-($originalPrice - $price));

            $ret['rawTotalSavingWithTax'] = $totalSavingWithTax;
            $ret['totalSavingWithTax'] = E::price(-($originalPriceWithTax - $priceWithTax));
            $ret['coupons'] = $coupons;


            return $ret;


        } catch (\Exception $e) {
            XLog::error("[Ekom module] - CouponLayer.applyCouponBag: $e");
            return false;
        }
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

