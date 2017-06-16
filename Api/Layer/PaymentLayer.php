<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;
use QuickPdo\QuickPdo;

class PaymentLayer
{

    public function getUserPaymentMethodId2Names($userId, $shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;
        $userId = (int)$userId;


        return A::cache()->get("Ekom.PaymentLayer.getUserPaymentMethodId2Names.$shopId.$userId", function () use ($shopId, $userId) {

            return QuickPdo::fetchAll("
select m.id, m.name

from ek_user_has_payment_method h 
inner join ek_payment_method m on m.id=h.payment_method_id 

where h.user_id=$userId 
and h.shop_id=$shopId
order by h.`order` asc 
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

        }, [
            "ek_user_has_payment_method.create",
            "ek_user_has_payment_method.delete.$userId.$shopId",
            "ek_user_has_payment_method.update.$userId.$shopId",
            "ek_payment_method",
        ]);
    }

    public function getUserPaymentMethods($userId, $shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;
        $userId = (int)$userId;
        a($userId, $shopId);


        return A::cache()->get("Ekom.PaymentLayer.getUserPaymentMethods.$shopId.$userId", function () use ($shopId, $userId) {

            return QuickPdo::fetchAll("
select
m.id, 
m.name,
h.options

from ek_user_has_payment_method h 
inner join ek_payment_method m on m.id=h.payment_method_id 

where h.user_id=$userId 
and h.shop_id=$shopId
order by h.`order` asc 
        ");

        }, [
            "ek_user_has_payment_method.create",
            "ek_user_has_payment_method.delete.$shopId",
            "ek_user_has_payment_method.update.$shopId",
            "ek_payment_method",
        ]);
    }

    public function getShopPaymentMethodId2Names($shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;


        return A::cache()->get("Ekom.PaymentLayer.getShopPaymentMethodId2Names.$shopId", function () use ($shopId) {

            return QuickPdo::fetchAll("
select
m.id, 
m.name


from ek_shop_has_payment_method h 
inner join ek_payment_method m on m.id=h.payment_method_id 

where h.shop_id=$shopId
order by h.`order` asc 
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);

        }, [
            "ek_shop_has_payment_method.create",
            "ek_shop_has_payment_method.delete.$shopId",
            "ek_shop_has_payment_method.update.$shopId",
            "ek_payment_method",
        ]);
    }


    public function getShopPaymentMethods($shopId = null)
    {
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
        }
        $shopId = (int)$shopId;


        return A::cache()->get("Ekom.PaymentLayer.getShopPaymentMethods.$shopId", function () use ($shopId) {

            return QuickPdo::fetchAll("
select
m.id, 
m.name,
h.configuration

from ek_shop_has_payment_method h 
inner join ek_payment_method m on m.id=h.payment_method_id 

where h.shop_id=$shopId
order by h.`order` asc 
        ");

        }, [
            "ek_shop_has_payment_method.create",
            "ek_shop_has_payment_method.delete.$shopId",
            "ek_shop_has_payment_method.update.$shopId",
            "ek_payment_method",
        ]);
    }


    /**
     * @param null $shopId , if null the current shopId will be used
     * @param null $userId , if null the session user will be used
     * @return array of payment method id (ek_payment_method.id)
     */
    public function getShopPaymentMethodBlockModels($shopId = null, $userId = null)
    {
        $rows = $this->getShopPaymentMethods($shopId);
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $ret = [];
        $all = $coll->all();


        if (null === $userId) {
            $userId = SessionUser::getValue("id");
        }


        $set = [];
        foreach ($rows as $row) {
            $set[] = $row['id'];
        }
        $preferredId = (int)$this->getPreferredPaymentMethodId($set, $shopId, $userId);
//


        $preferredWasFound = false;
        foreach ($rows as $row) {
            $name = $row['name'];
            $id = (int)$row['id'];
            if (array_key_exists($name, $all)) {
                $handler = $all[$name];
                $arr = $handler->getPaymentMethodBlockModel();

                if ($preferredId === $id) {
                    $preferredWasFound = true;
                    $arr['is_preferred'] = true;
                } else {
                    $arr['is_preferred'] = false;
                }
                $ret[$row['id']] = $arr;
            }
        }


        if (false === $preferredWasFound && array_key_exists(0, $rows)) {
            $rows[0]['is_preferred'] = true;
        }
        return $ret;
    }


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

        return A::cache()->get("Ekom.PaymentLayer.getUserPreferredPaymentMethodId.$userId.$shopId", function () use ($userId, $shopId) {

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

        return A::cache()->get("Ekom.PaymentLayer.getShopPreferredPaymentMethodId.$shopId", function () use ($shopId) {
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


    public function getPreferredPaymentMethodId(array $set, $shopId, $userId)
    {
        /**
         * The preferred payment method can be defined in two locations:
         *
         * - ek_shop_has_payment_method
         * - ek_user_has_payment_method (has precedence if defined here)
         */
        $userPreferredMethods = $this->getUserPaymentMethodId2Names($userId, $shopId);
        $shopPreferredMethods = $this->getShopPaymentMethodId2Names($shopId);
        $available = [];
        foreach ($userPreferredMethods as $id => $name) {
            if (array_key_exists($id, $shopPreferredMethods)) {
                $available[$id] = true;
            }
        }

        foreach ($available as $id => $bool) {
            if (in_array($id, $set, true)) {
                return $id;
            }
        }

        return key($shopPreferredMethods);
    }
}