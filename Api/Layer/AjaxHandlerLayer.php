<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\X;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\PaymentMethodHandler\Collection\PaymentMethodHandlerCollectionInterface;

/**
 * I noticed that my controller code was too verbose, while I wanted to have a thin controller layer.
 *
 * This object helps alleviate the controller's responsibilities
 * by centralizing all ajax logic in it, just in case the logic should be
 * re-used (at least that's the idea, although there are still a lot of verbose
 * controllers out there, and no time to fix them...)
 *
 */
class AjaxHandlerLayer
{


    /**
     * @return array (thought for json services)
     *
     * - type: success|error
     * - orderModel: only in case of success,
     *                  array representing the order model
     * - msg: in case of error
     *              This message can be safely displayed in the front end.
     *
     *
     *
     * What kind of public errors?
     *
     * - the payment layer failed for some reasons
     * - some crucial config options are missing (for instance the number of the credit card to use,
     *          which could happen if the user has no card at all for instance)
     * - others...
     *
     *
     *
     */
    public function handleCheckoutPlaceOrder()
    {

        $out = [];

        try {
            $checkoutLayer = EkomApi::inst()->checkoutLayer();
            $reference = EkomApi::inst()->orderLayer()->getUniqueReference();


            $model = $checkoutLayer->getOrderModel();

            $model['reference'] = $reference;


            $out['orderModel'] = $model;


            $paymentMethodName = $model['paymentMethodName'];
            /**
             * @var $col PaymentMethodHandlerCollectionInterface
             */
            $col = X::get("Ekom_getPaymentMethodHandlerCollection");
            if (null !== ($handler = $col->get($paymentMethodName, false))) {


                try {


                    $transactionData = $handler->pay($model);
                    $res = $checkoutLayer->placeOrder($reference, $transactionData);
                    if (true === $res) {
                        $type = "success";
                        $out['orderModel'] = $model;
                    } else {
                        $type = "error";
                        $out['msg'] = "The order couldn't be placed, please contact the webmaster";
                    }


                } catch (\Exception $e) {
                    $type = "error";
                    $out['msg'] = "A payment error occurred, please contact the webmaster";
                }


            } else {
                throw new \Exception("PaymentMethodHandler not found with name: $paymentMethodName");
            }

        } catch (\Exception $e) {
            $type = 'error';
            XLog::error("$e");
            $out['msg'] = "An error occurred, please contact the webmaster";
        }

        $out['type'] = $type;
        return $out;
    }


}