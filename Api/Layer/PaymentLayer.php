<?php


namespace Module\Ekom\Api\Layer;


use ArrayToString\ArrayToStringTool;
use Core\Services\A;
use Core\Services\X;
use Kamille\Services\XLog;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;
use Module\Ekom\PaymentMethodHandler\PaymentMethodHandlerInterface;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 * paymentMethodModel
 * ========================
 * - id: int,
 * - name: int,
 * - configuration: array (depends on the payment method)
 *
 *
 */
class PaymentLayer
{

    /**
     * @param $paymentMethodId
     * @return PaymentMethodHandlerInterface
     * @throws EkomException
     */
    public static function getPaymentMethodHandlerById($paymentMethodId)
    {
        /**
         * @var $collection PaymentMethodHandlerCollectionInterface
         */
        $collection = X::get("Ekom_getPaymentMethodHandlerCollection");
        $paymentMethodId = (int)$paymentMethodId;
        $paymentMethodName = QuickPdo::fetch("select name from ek_payment_method where id=$paymentMethodId", [], \PDO::FETCH_COLUMN);
        if (false === $paymentMethodName) {
            throw new EkomException("PaymentMethodHandler not found with id $paymentMethodId");
        }
        return $collection->get($paymentMethodName);
    }


    /**
     * @return array of id => item, each of which:
     *      - id: the id of the handler
     *      - name: the name of the handler
     *      - model: array ( the model returned by the PaymentMethodHandlerInterface.getModel method )
     *      - config: array (from the database ek_shop_has_payment_method.configuration )
     *      - selected: bool, whether this payment method item has focus
     */
    public static function getPaymentMethodHandlersItems($shopId = null, $currentPaymentMethodId = null)
    {
        $methods = self::getShopPaymentMethods($shopId);
        $ret = [];
        /**
         * @var $collection PaymentMethodHandlerCollectionInterface
         */
        $collection = X::get("Ekom_getPaymentMethodHandlerCollection");

        foreach ($methods as $method) {
            $id = $method['id'];
            $name = $method['name'];
            $conf = $method['configuration'];
            $handler = $collection->get($name);
            $model = $handler->getModel();


            $ret[$id] = [
                "id" => $id,
                "name" => $name,
                "model" => $model,
                "config" => $conf,
                "selected" => (int)$id === (int)$currentPaymentMethodId,
            ];
        }
        return $ret;
    }


    /**
     * @param null $shopId
     * @return int
     * @throws EkomException
     */
    public static function getPreferredPaymentMethodId($shopId = null)
    {
        $allMethods = self::getShopPaymentMethods($shopId);
        if (count($allMethods) > 0) {
            $row = array_shift($allMethods);
            return (int)$row['id'];
        }
        throw new EkomException("This shop must have at least one payment method assigned to it before you can continue");
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Return the payment methods available for a given shop.
     * Each item:
     * - id: string
     * - name: string
     * - configuration: array
     */
    private static function getShopPaymentMethods($shopId = null)
    {
        $shopId = E::getShopId($shopId);
        return A::cache()->get("Ekom.PaymentLayer.getShopPaymentMethods.$shopId", function () use ($shopId) {

            $ret = QuickPdo::fetchAll("
select
m.id, 
m.name,
h.configuration

from ek_shop_has_payment_method h 
inner join ek_payment_method m on m.id=h.payment_method_id 

where h.shop_id=$shopId
order by h.`order` asc 
        ");

            foreach ($ret as $k => $item) {
                $conf = unserialize($item['configuration']);
                if (false === $conf) {
                    $conf = [];
                }
                $item['configuration'] = $conf;
                $ret[$k] = $item;
            }

            return $ret;

        });
    }


    //--------------------------------------------
    // DEPRECATED BELOW
    //--------------------------------------------
    /**
     * @return array:paymentMethodModel
     * @see PaymentLayer
     * @throws EkomException if something wrong happens
     */
    public static function getDefaultPaymentMethod($shopId = null)
    {
        $allMethods = self::getShopPaymentMethods($shopId);
        if (count($allMethods) > 0) {
            $row = array_shift($allMethods);
            return [
                'id' => $row['id'],
                'name' => $row['name'],
                'configuration' => unserialize($row['configuration']),
            ];
        }
        throw new EkomException("This shop must have at least one payment method assigned to it before you can continue");
    }


    public function getPaymentDetails($methodName, array $userMethodOptions = [])
    {
        /**
         * @var $coll PaymentMethodHandlerCollectionInterface
         */
        $coll = X::get("Ekom_getPaymentMethodHandlerCollection");
        $handler = $coll->get($methodName, false);
        if (null === $handler) {
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


    public function getPaymentMethods($shopId = null)
    {
        return self::getShopPaymentMethods($shopId);
    }


    public function getPaymentMethodNameById($id, $shopId = null)
    {
        $methods = self::getShopPaymentMethods($shopId);
        foreach ($methods as $method) {
            if ((int)$method['id'] === (int)$id) {
                return $method['name'];
            }
        }
        return false;
    }

    private static function getPaymentMethodName2Ids($shopId = null)
    {
        $ret = [];
        $methods = self::getShopPaymentMethods($shopId);
        foreach ($methods as $method) {
            $ret[$method['name']] = $method['id'];
        }
        return $ret;
    }

}