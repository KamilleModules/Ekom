<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\Pdf\PdfHtmlInfoInterface;

class HooksHelper
{


    public static function Ekom_PdfHtmlInfo_decorate($pdfId, PdfHtmlInfoInterface $pdfHtmlInfo)
    {

        $appDir = Z::appDir();
        $pdfDir = $appDir . "/pdf";

        switch ($pdfId) {
            case "invoice":

                $pdfHtmlInfo->prepareByHtmlCallback(function ($pdfId, array $params = []) use ($pdfDir) {

                    if (true === E::userIsConnected()) {
                        $userId = E::getUserId();
                        $invoiceId = self::getParam("invoice_id", $params);
                        $template = $pdfDir . "/$pdfId.tpl.php";


                        if (false !== ($invoice = InvoiceLayer::getInvoiceById($invoiceId))) {
                            if ((int)$userId === (int)$invoice['user_id']) {

                                $invoiceDetails = $invoice['invoice_details'];
                                $cartModel = $invoiceDetails['cartModel'];
                                $paymentDetails = $invoiceDetails['payment_method_details'];
                                $repaymentSchedule = (array_key_exists("repayment_schedule", $paymentDetails)) ? $paymentDetails['repayment_schedule'] : [];

                                ob_start();
                                require_once $template;
                                return ob_get_clean();
                            } else {
                                throw new \Exception("User $userId doesn't own invoice $invoiceId");

                            }
                        } else {
                            throw new \Exception("Could not get invoice #$invoiceId for user $userId");
                        }

                    } else {

                        throw new EkomUserMessageException("Veuillez vous connecter");
                    }
                });
                break;
            default:
                break;
        }
    }


    public static function Ekom_CheckoutPageUtil_onStepCompleted($stepName, array $data)
    {
        throw new \Exception("todo, inject shipping into CurrentCheckoutData");
        switch ($stepName) {
            case "shipping":
                break;
            case "payment":
                break;
            default:
                break;
        }
    }

    public static function Ekom_ProductBox_collectGeneralContext(array &$data)
    {
        $data["shop_id"] = E::getShopId();
        $data["lang_id"] = E::getLangId();

        /**
         * @todo-ling: implement currency change in ekom !!
         */
        $data["date"] = date('Y-m-d');
        $data["currency_id"] = ApplicationRegistry::get("ekom.currency_id");
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getParam($paramName, array $params)
    {
        if (array_key_exists($paramName, $params)) {
            return $params[$paramName];
        }
        throw new \Exception("Missing parameter: $paramName");
    }
}