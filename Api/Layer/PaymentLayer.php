<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;
use QuickPdo\QuickPdo;

class PaymentLayer
{

    public function getPaymentMethodBlockModels()
    {
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $ret = [];
        $all = $coll->all();
        foreach ($all as $handler) {
            $ret[] = $handler->getPaymentMethodBlockModel();
        }
        return $ret;
    }


    public function getSelectableItemById($id)
    {
        $ret = null;
        $blocks = $this->getPaymentMethodBlockModels();
        $found = false;
        foreach ($blocks as $block) {
            $items = $block['items'];
            foreach ($items as $item) {
                if ($id === $item['id']) {
                    $ret = $item;
                    $found = true;
                    break;
                }
            }
        }
        if (false === $found) {
            throw new \Exception("No selectable item found with id $id");
        }
        return $ret;
    }


    /**
     * @return false|int, false if the user doesn't have a preferred payment method
     */
    public function getUserPreferredPaymentMethodId($userId, $shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }

        $userId = (int)$userId;
        $shopId = (int)$shopId;

        return A::cache()->get("PaymentLayer.getUserPreferredPaymentMethodId.$userId.$shopId", function () use ($userId, $shopId) {

            if (false !== ($row = QuickPdo::fetch("
select payment_method_id 
from ek_user_has_payment_method
where user_id=$userId        
and shop_id=$shopId        
order by `order` asc                
        "))
            ) {
                return $row['payment_method_id'];
            }
            return false;
        }, [
            "ek_user_has_payment_method.create",
            "ek_user_has_payment_method.update.$userId",
            "ek_user_has_payment_method.delete.$userId",
        ]);

    }


    /**
     * @return false|int, false if the shop doesn't have a preferred payment method
     */
    public function getShopPreferredPaymentMethodId($shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;

        return A::cache()->get("PaymentLayer.getShopPreferredPaymentMethodId.$shopId", function () use ($shopId) {
            if (false !== ($row = QuickPdo::fetch("
select payment_method_id 
from ek_shop_has_payment_method
where shop_id=$shopId        
order by `order` asc                
        "))
            ) {
                return (int)$row['payment_method_id'];
            }
            return false;
        }, [
            "ek_shop_has_payment_method.create",
            "ek_shop_has_payment_method.delete.$shopId",
            "ek_shop_has_payment_method.update.$shopId",
        ]);
    }


    /**
     * Tries to return the user preferred payment method id first, and if she doesn't have one yet,
     * return the shop's preferred payment method id (the one with the lowest order column value).
     * If no payment method exists for this shop, returns false.
     *
     *
     * @return false|int
     */
    public function getDefaultPaymentMethodId($userId, $shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }

        $userId = (int)$userId;
        $shopId = (int)$shopId;
        if (false !== ($id = $this->getUserPreferredPaymentMethodId($userId, $shopId))) {
            return $id;
        }
        return $this->getShopPreferredPaymentMethodId($shopId);
    }

}