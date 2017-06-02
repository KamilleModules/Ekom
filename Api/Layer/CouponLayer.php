<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class CouponLayer
{




    public function tryAddCouponByCode($code, array $couponBag){

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
     * @param $id
     * @param bool $withCartDiscounts
     * @return array of couponInfo, which has the following structure:
     *
     *
     * - code: string, the code of the coupon
     * - active: 1|0, whether or not the coupon code is active
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
}
