<?php


namespace Controller\Ekom\Front\Checkout;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Controller\Ekom\EkomFrontController;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Session\EkomSession;
use Module\Ekom\Utils\E;


class CheckoutOnePageThankYouControllerOld extends EkomFrontController
{
    protected function prepareClaws()
    {

        parent::prepareClaws();

        $this->requiresClawsConnectedUser();


        $model = [];

        $orderId = EkomSession::get("order.last");
        if (null !== $orderId) {

            if ('singleAddress' === E::conf("checkoutMode")) {

                $info = EkomApi::inst()->orderLayer()->getOrderInfo($orderId);


                $section = $info['order_details']['orderSections']['sections'][0];

                $products = [];
                foreach ($section['productsInfo'] as $p) {
                    $products[] = [
                        "label" => $p['label'] . ' (ref: ' . $p['ref'] . ')',
                        "quantity" => $p['quantity'],
                        "img" => $p['image'],
                        "uri" => $p['uri_card_with_ref'],
                    ];
                }


                $model['reference'] = $info['reference'];
                $model['trackingNumber'] = $info['tracking_number'];
                $model['estimatedDeliveryDate'] = $section['estimatedDeliveryDate'];
                $model['shippingUserName'] = $info['shipping_address']['fName'];
                $model['products'] = $products;
                $model['uriCustomerOrders'] = E::link("Ekom_customerOrders");
                $model['uriMyAccount'] = E::link("Ekom_customerDashboard");

            } else {
                throw new \Exception("Not implemented yet");
            }

        } else {
            $model['error'] = "A problem occurred";
            XLog::error("[Ekom module] - CheckoutOnePageThankYouController: key ekom.order.last not found in session");
        }



        $products = EkomApi::inst()->productSelectionLayer()->getProductBoxModelsByAnyInCategoryAndUp("kettlebell");
        $productsAlt = EkomApi::inst()->productSelectionLayer()->getProductBoxModelsByAnyInCategoryAndUp("bandes");


        $this->getClaws()
            ->setLayout("sandwich_1c/default")
            ->setWidget("maincontent.orderSummary", ClawsWidget::create()
                ->setTemplate("Ekom/Checkout/OrderThankYouSummary/default")
                ->setConf($model)
            )
            ->setWidget("maincontent.featuredProducts", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "Nous vous recommandons",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts2", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "Recommandations selon votre commande",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts3", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "Recommandations pour vous en Jeux et Jouets",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts4", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "Recommandations pour vous en Livres",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts5", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "Inspirés par les tendances générales de vos achats",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts6", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "xxxx",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts2", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "Recommandations pour vous en Cuisine & Maison",
                    "products" => $productsAlt,
                ])
            )
            ->setWidget("maincontent.backToSiteButton", ClawsWidget::create()
                ->setTemplate("Ekom/CarouselProducts/default")
                ->setConf([
                    "title" => "xxxx",
                    "products" => $products,
                ])
            )
            ->setWidget("maincontent.featuredProducts7", ClawsWidget::create()
                ->setTemplate("Ekom/BackToSiteButton/prototype")
                ->setConf([
                    "title" => "Poursuivre mes achats",
                ])
            );
    }



//    public function render()
//    {
//
//        if (true === SessionUser::isConnected()) {
//
//
//            $model = [];
//
//            $orderId = EkomSession::get("order.last");
//            if (null !== $orderId) {
//
//                if ('singleAddress' === E::conf("checkoutMode")) {
//
//                    $info = EkomApi::inst()->orderLayer()->getOrderInfo($orderId);
//
//
//                    $section = $info['order_details']['orderSections']['sections'][0];
//
//                    $products = [];
//                    foreach ($section['productsInfo'] as $p) {
//                        $products[] = [
//                            "label" => $p['label'] . ' (ref: ' . $p['ref'] . ')',
//                            "quantity" => $p['quantity'],
//                            "img" => $p['image'],
//                            "uri" => $p['uri_card_with_ref'],
//                        ];
//                    }
//
//
//                    $model['reference'] = $info['reference'];
//                    $model['trackingNumber'] = $info['tracking_number'];
//                    $model['estimatedDeliveryDate'] = $section['estimatedDeliveryDate'];
//                    $model['shippingUserName'] = $info['shipping_address']['fName'];
//                    $model['products'] = $products;
//                    $model['uriCustomerOrders'] = E::link("Ekom_customerOrders");
//
//                } else {
//                    throw new \Exception("Not implemented yet");
//                }
//
//            } else {
//                $model['error'] = "A problem occurred";
//                XLog::error("[Ekom module] - CheckoutOnePageThankYouController: key ekom.order.last not found in session");
//            }
//
//
//            return $this->renderByViewId("Ekom/checkout/checkoutOnePageThankYou", LawsConfig::create()->replace([
//                'widgets' => [
//                    'maincontent.orderSummary' => [
//                        'grid' => "1",
//                        'tpl' => "Ekom/Checkout/OrderThankYouSummary/default",
//                        'conf' => $model,
//                    ],
//                ],
//            ]));
//        } else {
//            return $this->requiresConnectedUser();
//        }
//    }
}