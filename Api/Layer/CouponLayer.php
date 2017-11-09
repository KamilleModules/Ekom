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
 * CouponInfo
 * ================
 * - code: string, the code of the coupon
 * - active: 1|0, whether or not the coupon code is considered active when added to the coupon bag
 * - mode: unique|mergeWithSame|merge, see the database.md document for more info
 * - priority: int, the priority helper for more
 * - label: string, the coupon label
 * - discounts: array of target => discount. each discount is an item with the following structure.
 *                  See database.md for more info.
 *          - target
 *          - type
 *          - operand
 *          - label
 *
 *
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


    /**
     *
     * Use this method to apply some couponInfo to a number.
     *
     * @param string|null $target , the target filter.
     *                              If null, all discounts will apply
     *                              If string, only the discounts matching the target will apply
     *
     * @return array|false, false in case of errors. Otherwise the following array:
     *              - discountPriceRaw, the unformatted discount price
     *              - discountPrice, the formatted version of discountPriceRaw
     *              - totalSavingRaw, the unformatted amount of saving
     *              - totalSaving, the formatted version of totalSavingRaw
     *              - coupons: array of couponDetail, each couponDetail is an array with the following structure:
     *
     *                      - target, the target being used
     *                      - code: coupon code
     *                      - label: coupon label
     *                      - savingRaw: the unformatted amount of saving for the ensemble of the discounts for this coupon
     *                      - saving: the formatted version of savingRaw
     *                      - discounts: array of $target => discountDetails.
     *                                  Each discountDetail is an array with the following structure:
     *
     *                                - label: string, the discount label
     *                                - old: float, just a reference to the price BEFORE the discount was applied
     *                                - newPriceRaw: the unformatted price (AFTER the discount was applied)
     *                                - newPrice: the formatted version of newPriceRaw
     */
    public static function applyCouponBag($price, array $couponInfoItems, $target = null)
    {

        try {
            $ret = [];
            $originalPrice = $price;
            $coupons = [];
            foreach ($couponInfoItems as $info) {
                $coupon = [
                    'label' => $info['label'],
                ];

                if (true === self::testCouponConditionsByCode($info['code'])) {

                    $_discounts = [];
                    foreach ($info['discounts'] as $_target => $discounts) {
                        if (null === $target || $_target === $target) {
                            $temp = $price;

                            foreach ($discounts as $k => $discount) {

                                $_discount = [];
                                $old = $price;
                                $price = DiscountLayer::applyDiscountInfoToPrice($discount, $price);

                                $_discount['old'] = $old;
                                $_discount['newPrice'] = E::price($price);
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


    public static function getCouponInfo($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select * from ek_coupon where id=$id");
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

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function testCouponConditionsByCode($code)
    {
        /**
         * Note: we could imagine creating a hook here, and modules would decide whether or not the coupon
         * should apply.
         */
        return true;
    }


    private function getCouponBagInfo(array $couponBag, $witCartDiscounts = false)
    {
        $ret = [];
        foreach ($couponBag as $id) {
            $ret[$id] = $this->getCouponInfo($id, $witCartDiscounts);
        }
        return $ret;
    }


}

