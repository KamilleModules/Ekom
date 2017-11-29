<?php


namespace Module\Ekom\Model\Front\Checkout;


use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Module\Ekom\Api\EkomApi;
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
            $info = EkomApi::inst()->orderLayer()->getOrderInfo($orderId);
            $items = $info['order_details']['cartModel']['items'];
            $estimatedDeliveryDate = null;
            $shippingDetails = $info['order_details']['cartModel']['shippingDetails'];
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


            $model['reference'] = $info['reference'];
            $model['trackingNumber'] = $info['tracking_number'];
            $model['estimatedDeliveryDate'] = $estimatedDeliveryDate;
            $model['shippingUserName'] = $info['shipping_address']['fName'];
            $model['products'] = $products;
            $model['uriCustomerOrders'] = E::link("Ekom_customerOrders");
            $model['uriMyAccount'] = E::link("Ekom_customerDashboard");


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