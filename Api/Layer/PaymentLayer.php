<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\PaymentMethodConfig\Collection\PaymentMethodConfigCollection;
use Module\Ekom\PaymentMethodConfig\Collection\PaymentMethodConfigCollectionInterface;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;
use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;
use QuickPdo\QuickPdo;

class PaymentLayer
{


    public function shopHasPaymentMethod($shopId, $id)
    {
        $shopId = (int)$shopId;
        $id = (int)$id;
        if (false !== QuickPdo::fetch("
select shop_id 
from ek_shop_has_payment_method
where shop_id=$shopId and payment_method_id=$id
        ")) {
            return true;
        }
        return false;
    }


    public function getDefaultPaymentMethodId($userId)
    {
        if (false !== ($row = EkomApi::inst()->orderLayer()->getLastOrderByUserId($userId))) {
            az(__FILE__);
        }
        return false;
    }



//    public function getShopPaymentMethodId2Names($shopId = null)
//    {
//        if (null === $shopId) {
//            EkomApi::inst()->initWebContext();
//            $shopId = ApplicationRegistry::get("ekom.shop_id");
//        }
//        $shopId = (int)$shopId;
//
//
//        return A::cache()->get("Ekom.PaymentLayer.getShopPaymentMethodId2Names.$shopId", function () use ($shopId) {
//
//            return QuickPdo::fetchAll("
//select
//m.id,
//m.name
//
//
//from ek_shop_has_payment_method h
//inner join ek_payment_method m on m.id=h.payment_method_id
//
//where h.shop_id=$shopId
//order by h.`order` asc
//        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
//
//        }, [
//            "ek_shop_has_payment_method.create",
//            "ek_shop_has_payment_method.delete.$shopId",
//            "ek_shop_has_payment_method.update.$shopId",
//            "ek_payment_method",
//        ]);
//    }


    /**
     * @todo-ling: every block model related code is deprecated...,
     * and replaced by PaymentConfig
     *
     * @param $id , paymentMethod id
     * @param array $options
     */
    public function getConfiguredPaymentBlockModel($id, array $options = [], $shopId = null)
    {
        $rows = $this->getShopPaymentMethods($shopId);
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $all = $coll->all();
        foreach ($rows as $row) {
            if ((int)$id === (int)$row['id']) {
                $name = $row['name'];
                if (array_key_exists($name, $all)) {
                    $handler = $all[$name];
                    /**
                     * @var $handler PaymentMethodHandlerInterface
                     */
                    return $handler->getPaymentMethodItem($options);
                }
            }
        }
        return false;
    }


    /**
     * @return array
     *              - name => [
     *                  - id: the payment method id
     *                  - (all properties of payment config)
     *              ]
     */
    public function getPaymentMethodConfigs()
    {
        $ret = [];
        /**
         * @var $coll PaymentMethodConfigCollectionInterface
         */
        $coll = X::get("Ekom_getPaymentMethodConfigCollection");
        $all = $coll->all();
        $name2Ids = $this->getPaymentMethodName2Ids();
        foreach ($all as $name => $item) {
            $ret[$name] = $item->getConfig();
            $ret[$name]['id'] = $name2Ids[$name];
        }
        return $ret;
    }


    /**
     * @return array|false, false if there is no payment method in the shop
     *      - id
     *      - name
     *      - configuration (array)
     */
    public function getDefaultPaymentMethod($shopId = null)
    {
        $allMethods = $this->getShopPaymentMethods($shopId);
        if (count($allMethods) > 0) {
            $row = current($allMethods);
            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'configuration' => unserialize($row['configuration']),
            ];
        }
        return false;
    }

    /**
     *
     * Return array of paymentMethodId => paymentMethodBlockModel,
     * in order of preference (the preferred first)
     *
     * If the second argument is passed (paymentMethodId),
     * it will set the is_preferred entry to true for the corresponding payment method.
     * If it's null, then the first payment method will be the preferred one.
     *
     *
     *
     * @return array of paymentMethodId => paymentMethodBlockModel
     *
     */
    public function getShopPaymentMethodBlockModels($shopId = null, $paymentMethodId = null, array $paymentMethodOptions = null)
    {
        if (null === $paymentMethodOptions) {
            $paymentMethodOptions = [];
        }
        $rows = $this->getShopPaymentMethods($shopId);
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $ret = [];
        $all = $coll->all();


        $c = 0;
        foreach ($rows as $row) {
            $name = $row['name'];
            if (array_key_exists($name, $all)) {
                $handler = $all[$name];
                $arr = $handler->getPaymentMethodBlockModel($paymentMethodOptions);

                if (null === $paymentMethodId) {
                    if (0 === $c++) {
                        $arr['is_preferred'] = true;
                    } else {
                        $arr['is_preferred'] = false;
                    }
                } else {

                    if ((int)$row['id'] === (int)$paymentMethodId) {
                        $arr['is_preferred'] = true;
                    } else {
                        $arr['is_preferred'] = false;
                    }
                }

                $ret[$row['id']] = $arr;
            }
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
            /**
             * @var $handler PaymentMethodHandlerInterface
             */
            $ret[] = $handler->getPaymentMethodBlockModel();
        }
        return $ret;
    }


    public function getPaymentMethods($shopId = null)
    {
        return $this->getShopPaymentMethods($shopId);
    }

    public function getPaymentMethodName2Ids($shopId = null)
    {
        $ret = [];
        $methods = $this->getShopPaymentMethods($shopId);
        foreach ($methods as $method) {
            $ret[$method['name']] = $method['id'];
        }
        return $ret;
    }



//    /**
//     * @return false|int, false if the shop doesn't have a preferred payment method
//     */
//    public function getShopPreferredPaymentMethodId($shopId = null)
//    {
//        if (null === $shopId) {
//            EkomApi::inst()->initWebContext();
//            $shopId = ApplicationRegistry::get("ekom.shop_id");
//        }
//        $shopId = (int)$shopId;
//
//        return A::cache()->get("Ekom.PaymentLayer.getShopPreferredPaymentMethodId.$shopId", function () use ($shopId) {
//            if (false !== ($row = QuickPdo::fetch("
//select payment_method_id
//from ek_shop_has_payment_method
//where shop_id=$shopId
//order by `order` asc
//        "))
//            ) {
//                return (int)$row['payment_method_id'];
//            }
//            return false;
//        }, [
//            "ek_shop_has_payment_method.create",
//            "ek_shop_has_payment_method.delete.$shopId",
//            "ek_shop_has_payment_method.update.$shopId",
//        ]);
//    }


    /**
     * Tries to return the user preferred payment method id first, and if she doesn't have one yet,
     * return the shop's preferred payment method id (the one with the lowest order column value).
     * If no payment method exists for this shop, returns false.
     *
     *
     * @return false|int
     */
//    public function getDefaultPaymentMethodId($userId, $shopId = null)
//    {
//        if (null === $shopId) {
//            EkomApi::inst()->initWebContext();
//            $shopId = ApplicationRegistry::get("ekom.shop_id");
//        }
//
//        $userId = (int)$userId;
//        $shopId = (int)$shopId;
//        if (false !== ($id = $this->getUserPreferredPaymentMethodId($userId, $shopId))) {
//            return $id;
//        }
//        return $this->getShopPreferredPaymentMethodId($shopId);
//    }
//
//
//    public function getPreferredPaymentMethodId(array $set, $shopId, $userId)
//    {
//        /**
//         * The preferred payment method can be defined in two locations:
//         *
//         * - ek_shop_has_payment_method
//         * - ek_user_has_payment_method (has precedence if defined here)
//         */
//        $userPreferredMethods = $this->getUserPaymentMethodId2Names($userId, $shopId);
//        $shopPreferredMethods = $this->getShopPaymentMethodId2Names($shopId);
//        $available = [];
//        foreach ($userPreferredMethods as $id => $name) {
//            if (array_key_exists($id, $shopPreferredMethods)) {
//                $available[$id] = true;
//            }
//        }
//
//        foreach ($available as $id => $bool) {
//            if (in_array($id, $set, true)) {
//                return $id;
//            }
//        }
//
//        return key($shopPreferredMethods);
//    }


//--------------------------------------------
//
//--------------------------------------------
    /**
     * Return the payment methods available for a given shop.
     */
    private function getShopPaymentMethods($shopId = null)
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
}