<?php


namespace Module\Ekom\Status\Provider;


use Module\Ekom\Status\Action\EkomStatusAction;


/**
 * This status provider uses the following status logic:
 *
 * - the order is first placed by the user
 * - then we wait for the payment, we won't do anything without the payment being confirmed (i.e. the money is on our bank account)
 * - once the payment has been confirmed, we send the products
 *
 *
 *
 *
 */
class LeeStatusProvider extends StatusProvider
{
    public function __construct()
    {
        parent::__construct();
        $this->action2Codes = [
            EkomStatusAction::ACTION_ORDER_PLACED => 'orderPlaced',
            EkomStatusAction::ACTION_PAYMENT_ACCEPTED => 'paymentAccepted',
            EkomStatusAction::ACTION_SHIPPED => 'shipped',
        ];
    }


}