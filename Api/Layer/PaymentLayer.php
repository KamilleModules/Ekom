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


    public static function getPaymentMethodItemsWithNameAsKey()
    {
        return QuickPdo::fetchAll("select `name`, label from ek_payment_method", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getPaymentMethodItems()
    {
        return QuickPdo::fetchAll("select id, label from ek_payment_method", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    /**
     * @param $paymentMethodId
     * @return PaymentMethodHandlerInterface
     * @throws \Exception
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

    public static function getPaymentMethodNameById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select name from ek_payment_method where id=$id", [], \PDO::FETCH_COLUMN);
    }

    public static function getPaymentMethodNameAndLabelById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("select name, label from ek_payment_method where id=$id");
    }

    /**
     * @return array of id => item, each of which:
     *      - id: the id of the handler
     *      - name: the name of the handler
     *      - model: array ( the model returned by the PaymentMethodHandlerInterface.getModel method )
     *      - config: array (from the database ek_shop_has_payment_method.configuration )
     *      - selected: bool, whether this payment method item has focus
     * @throws \Exception
     */
    public static function getPaymentMethodHandlersItems($currentPaymentMethodId = null)
    {
        $methods = self::getShopPaymentMethods();
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
//            az(get_class($handler));
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
    public static function getPreferredPaymentMethodId()
    {
        $allMethods = self::getShopPaymentMethods();
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
    private static function getShopPaymentMethods()
    {
        return A::cache()->get("Ekom.PaymentLayer.getShopPaymentMethods", function ()  {

            $ret = QuickPdo::fetchAll("
select
id, 
name,
configuration

from ek_payment_method  
order by `order` asc 
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
    public static function getDefaultPaymentMethod()
    {
        $allMethods = self::getShopPaymentMethods();
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