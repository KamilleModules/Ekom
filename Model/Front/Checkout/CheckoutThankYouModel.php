<?php


namespace Module\Ekom\Model\Front\Checkout;


use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
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
        $model = [];

        if (null !== $orderId) {

            /**
             * @todo-ling: ensure that only the last order is browsable
             */
            $info = OrderLayer::getOrderInfo($orderId);
            $orderDetails = $info['order_details'];
            $items = $orderDetails['cartModel']['items'];
            $estimatedDeliveryDate = null;
            $shippingDetails = $orderDetails['cartModel']['shippingDetails'];
            if (array_key_exists("estimated_delivery_date", $shippingDetails)) {
                $estimatedDeliveryDate = $shippingDetails['estimated_delivery_date'];
            }


            $products = [];
            foreach ($items as $p) {
                $products[] = [
                    "label" => $p['label'] . ' (ref: ' . $p['ref'] . ')',
                    "quantity" => $p['quantityCart'],
                    "img" => $p['imageThumb'],
                    "uri" => $p['uri_card_with_details'],
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
            $products = ProductBoxLayer::getProductBoxListByCategoryName("kettlebell");
            $productsAlt = ProductBoxLayer::getProductBoxListByCategoryName("bandes");

            $model['products'] = $products;
            $model['productsAlt'] = $productsAlt;


        } else {
            return RedirectResponse::create(E::link("Ekom_home", [], true));
        }


        return $model;
    }
}