<?php


namespace Controller\Ekom\Front\Customer;


use Controller\Ekom\Front\CustomerController;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Utils\E;
use Module\EkomUserProductHistory\Api\EkomUserProductHistoryApi;
use Module\EkomUserTracker\Api\Layer\UserTrackerLayer;
use Module\ThisApp\Api\ThisAppApi;

class DashboardController extends CustomerController
{


    protected function prepareClaws()
    {
        parent::prepareClaws();


        $userId = E::getUserId();
        $lastOrderLink = E::link("Ekom_customerOrders") . '?sort=date&asc=0&page=1';
        $lastInfo = OrderLayer::getLastOrderInfoByUserId($userId);
        if (false !== $lastInfo) {
            $lastOrderLink .= "&open=" . $lastInfo['id'];
        }


        $nbPendingOrders = OrderLayer::getNbUserPendingOrders($userId);
        $pendingOrdersLink = E::link("Ekom_customerOrders") . '?status=pending';
        $canceledOrdersLink = E::link("Ekom_customerOrders") . '?status=canceled&sort=date&asc=0&page=1&nipp=1';
        $nbTotalPoints = ThisAppApi::inst()->userInfoLayer()->getTotalNbPointsByUserId($userId);
        $myPointsLink = E::link("Ekom_customerLoyaltyPoints");
        $nbWishListItems = EkomApi::inst()->wishListLayer()->getNbUserWishItems($userId);
        $uriWishList = E::link("Ekom_customerWishList");
        $nbHistoryItemsLastDay = UserTrackerLayer::getNbProductsVisitedTodayByUserId($userId);


        $uriHistory = E::link("Ekom_customerProductHistory");
        $uriShippingAddress = E::link("Ekom_customerAddressBook");
        $uriBillingAddress = E::link("Ekom_customerAddressBook");
        $uriPaymentMethods = E::link("Ekom_customerPaymentMethods");




        $this->getClaws()
            ->setWidget("maincontent.dashboard", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/Dashboard/leaderfit")
                ->setConf([
                    'lastOrderLink' => $lastOrderLink,
                    'nbPendingOrders' => $nbPendingOrders,
                    'pendingOrdersLink' => $pendingOrdersLink,
                    'canceledOrdersLink' => $canceledOrdersLink,
                    'nbLoyaltyPoints' => $nbTotalPoints,
                    'myPointsLink' => $myPointsLink,
                    'nbWishListItems' => $nbWishListItems,
                    'uriWishList' => $uriWishList,
                    'nbHistoryItemsLastDay' => $nbHistoryItemsLastDay,
                    'uriHistory' => $uriHistory,
                    'uriShippingAddress' => $uriShippingAddress,
                    'uriBillingAddress' => $uriBillingAddress,
                    'uriPaymentMethods' => $uriPaymentMethods,
                ])
            );
    }
}