<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Authenticate\SessionUser\SessionUser;
use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;
use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;
use QuickPdo\QuickPdo;

class PaymentLayer
{


    public function getPaymentDetails($methodName, array $userMethodOptions = [])
    {
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        $handler =$coll->get($methodName, false);
        if(null===$handler){
            throw new EkomApiException("Handler not found with method $methodName");
        }
        /**
         * @var $handler PaymentMethodHandlerInterface
         */
        return $handler->getPaymentDetails($userMethodOptions);
    }

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



    /**
     * @return array
     *              - name => [
     *                  - id: the payment method id
     *                  - (all properties of payment config)
     *              ]
     */
    public function getPaymentMethodHandlers()
    {
        $ret = [];
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        $all = $coll->all();
        $name2Ids = $this->getPaymentMethodName2Ids();
        foreach ($all as $name => $item) {
            $ret[$name] = $item->getConfig();
            if (!array_key_exists($name, $name2Ids)) {
                XLog::error("[Ekom module] - name $name doesn't exist in this shop: " . ArrayToStringTool::toPhpArray($name2Ids));
            }
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


    public function getPaymentMethodNameById($id, $shopId = null)
    {
        $methods = $this->getShopPaymentMethods($shopId);
        foreach ($methods as $method) {
            if ((int)$method['id'] === (int)$id) {
                return $method['name'];
            }
        }
        return false;
    }




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