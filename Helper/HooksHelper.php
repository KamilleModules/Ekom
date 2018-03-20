<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Back\Util\ApplicationSanityCheck\ApplicationSanityCheckUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\Pdf\PdfHtmlInfoInterface;
use QuickPdo\Helper\QuickPdoHelper;

class HooksHelper
{
    public static function NullosAdmin_layout_topNotifications(array &$topNotifications)
    {
        $errors = ApplicationSanityCheckUtil::getSessionErrors();
        foreach ($errors as $token => $params) {
            $msg = "";
            switch ($token) {
                case "a":
                    $msg = "The following countries require (at least one) translation: {codes}.<br>
                Go to the \"Super Admin > Country > Country translation\" page to fix this problem.
                ";
                    break;
                case "b":
                case "c":
                case "d":
                case "e":
                case "f":
                case "g":
                case "h":
                case "i":
                case "j":
                case "k":
                case "l":
                case "m":
                case "n":
                    $msg = "You need to add at least one {word} ({table})";
                    break;
                case "o":
                    $msg = "The following categories require (at least one) translation: {codes}.<br>
                Go to the \"Shop > Category\" page to fix this problem.
                ";
                    break;
                default:
                    break;
            }
            $tags = [];
            foreach ($params as $k => $v) {
                $tags['{' . $k . '}'] = $v;
            }
            /**
             * default translation system
             * @todo-ling: translate in other languages (i.e. add a real translation system)
             */
            $msg = str_replace(array_keys($tags), array_values($tags), $msg);
            $topNotifications[] = [
                "type" => "warning",
                "type" => "error",
                "title" => "Sanity Check Error:",
                "msg" => $msg,
            ];
        }
    }


    public static function Core_onQuickPdoInteractionAfter(array $params)
    {
        $method = $params['method'];
        $table = $params['table'];
        $activeMethod = QuickPdoHelper::getActiveMethod($method);
        ApplicationSanityCheckUtil::onQuickPdoDataAlterAfter($table, $activeMethod);
    }

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
        $data["date"] = date('Y-m-d');
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