<?php


namespace Module\Ekom\Model\Front\Checkout;


use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\MiniProductBoxLayer;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\ProductBoxLayer;
use Module\Ekom\Utils\E;

class CheckoutThankYouModel
{

    /**
     * @param null $orderId
     * @return array|HttpResponseInterface
     */
    public static function getModel($orderId = null)
    {
        if (true === E::userIsConnected()) {
            $userId = E::getUserId();
            $model = [];

            if (null !== $orderId) {

                /**
                 * @todo-ling: ensure that only the last order is browsable
                 */
                $info = OrderLayer::getOrderInfo($orderId);
                if ((int)$userId === (int)$info['user_id']) {


                    $orderDetails = $info['order_details'];
                    $cartModel = $orderDetails['cartModel']['cart'];
                    $items = $cartModel['items'];
                    $estimatedDeliveryDate = $cartModel['carrier_estimated_delivery_date'];


                    $products = [];
                    foreach ($items as $p) {
                        $products[] = [
                            "label" => $p['label'] . ' (ref: ' . $p['reference'] . ')',
                            "quantity" => $p['cart_quantity'],
                            "img" => $p['image'],
                            "uri" => $p['product_uri_with_details'],
                        ];
                    }

                    $lfPoints = 0;
                    if (array_key_exists("lfPoints", $orderDetails)) {
                        $lfPoints = array_sum($orderDetails["lfPoints"]);
                    }


                    $model['reference'] = $info['reference'];
//            $model['trackingNumber'] = $info['tracking_number'];
                    $model['estimatedDeliveryDate'] = $estimatedDeliveryDate;
                    $model['shippingUserName'] = $info['shipping_address']['fName'];
                    $model['products'] = $products;
                    $model['uriCustomerOrders'] = E::link("Ekom_customerOrders");
                    $model['uriMyAccount'] = E::link("Ekom_customerDashboard");
                    $model['paymentMethod'] = $info['order_details']['payment_method_name'];
                    $model['lfPoints'] = $lfPoints;


                    /**
                     * @todo-ling: change those fake/demo carousels
                     */
                    $products = MiniProductBoxLayer::getBoxesByCategoryName("kettlebell");
                    $productsAlt = MiniProductBoxLayer::getBoxesByCategoryName("bandes");

                    $model['products'] = $products;
                    $model['productsAlt'] = $productsAlt;
                    return $model;
                }
            }
        }
        return RedirectResponse::create(E::link("Ekom_home", [], true));


    }
}