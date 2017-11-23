<?php


namespace Module\Ekom\Utils\Checkout\StepOld;


use Module\Ekom\Api\EkomApi;

class LoginCheckoutStep extends BaseCheckoutStep
{
    public function listen(array &$doneData = null, array $defaults = [])
    {
        $response = null;
        $formModel = EkomApi::inst()->connexionLayer()->handleLoginForm($response);
        if (null !== $response) {
            $doneData = ['ok'];
            return $response;
        }
        return $formModel;
    }


}