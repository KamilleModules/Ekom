<?php


namespace Controller\Ekom\Front\Checkout;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Bat\UriTool;
use Controller\Ekom\EkomFrontController;
use Core\Services\X;
use Ingenico\Handler\IngenicoHandler;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Ling\Z;
use Kamille\Utils\Laws\Config\LawsConfig;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Module\PeiPei\OnTheFlyForm\CreditCardWallet\CreditCardOnTheFlyForm;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;


class CheckoutOnePageController extends EkomFrontController
{

    public function render()
    {

        $step = (array_key_exists('step', $_GET)) ? $_GET['step'] : 1;

        if (SessionUser::isConnected()) {


            $orderModel = EkomApi::inst()->checkoutLayer()->getOrderModel();
//            az($orderModel);


            $conf = [
                'orderModel' => $orderModel,
            ];
            //--------------------------------------------
            // MODULES
            //--------------------------------------------
            if ("PeiPei") {

                $appDir = Z::appDir();
                $h = IngenicoHandler::createByConfFile($appDir . "/www/ingenico.conf.php"); // change the path accordingly; depends on your system


                $res = $h->flexCheckout()->injectFlexFormToIframe("ingenico-payment", [
                    'CARD.BRAND' => "VISA",
                    'CARD.PAYMENTMETHOD' => "CreditCard",
                    'ALIAS.ORDERID' => "checkoutogone-" . date('Y-m-d H:i:s'),
                    'ALIAS.STOREPERMANENTLY' => "Y",
                    'PARAMETERS.ACCEPTURL' => "https://monplanning.ovh/back.php?accepturl=1",
                    'PARAMETERS.EXCEPTIONURL' => "https://monplanning.ovh/back.php?exceptionurl=1",
                    'PARAMETERS.DECLINEURL' => "https://monplanning.ovh/back.php?declineurl=1",
                ], true);
                $conf['peipei']['ingenicoFlexCheckoutFormTrigger'] = $res;


                $ccForm = new CreditCardOnTheFlyForm();

                $conf['peipei']['creditCardFormModel'] = $ccForm->getModel();
            }


            return $this->renderByViewId("Ekom/checkout/checkoutOnePage", LawsConfig::create()->replace([
                "widgets" => [
                    'checkout' => [
                        "conf" => $conf
                    ],
                ],
            ]));

        } else {
            return $this->requiresConnectedUser();
        }
    }


}