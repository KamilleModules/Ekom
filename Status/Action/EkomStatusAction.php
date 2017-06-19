<?php


namespace Module\Ekom\Status\Action;


class EkomStatusAction
{

    /**
     * This is the first step in the action chain.
     * An order entry has just been created in the database.
     */
    const ACTION_ORDER_PLACED = 'orderPlaced';

    /**
     * This action is invoked when the payment has been confirmed (i.e. the money is on your bank account)
     */
    const ACTION_PAYMENT_ACCEPTED = 'paymentAccepted';

    /**
     * This action should be invoked when the shop owner has sent the products.
     * There might be tracking number available for the user, depending on the chosen carrier.
     *
     */
    const ACTION_SHIPPED = 'shipped';


}