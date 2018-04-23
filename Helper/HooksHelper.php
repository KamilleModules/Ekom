<?php


namespace Module\Ekom\Helper;

use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Ling\Z;
use Kamille\Services\XConfig;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Api\Layer\UserVisitedProductReferencesLayer;
use Module\Ekom\Back\Util\ApplicationSanityCheck\ApplicationSanityCheckUtil;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\Pdf\PdfHtmlInfoInterface;
use QuickPdo\Helper\QuickPdoHelper;

class HooksHelper
{

    /**
     * @param array $tailModel
     * @param array $headModel, is the box model.
     * @see EkomModels::productBoxModel()
     *
     */
    public static function Ekom_ProductPage_decoratePageTailModel(array &$tailModel, array $headModel)
    {

        //--------------------------------------------
        // last visited
        //--------------------------------------------
        $lastVisited = UserVisitedProductReferencesLayer::getLastVisitedProductsByProductBox($headModel);
        $tailModel['lastVisited'] = $lastVisited;
    }


    /**
     * @param array $productBox
     * @see EkomModels::productBoxModel()
     */
    public static function Ekom_ProductGetInfoService_PageVisited(array $productBox)
    {
        UserVisitedProductReferencesLayer::addVisitedReferenceByProductBoxModel($productBox);
    }


    public static function FishMailer_collectVariables(array &$pool, string $template, string $mode)
    {

        $p = explode("/", $template);
        if ('Ekom' === $p[0]) {

            $lang = $p[1];
            $templateName = array_pop($p);
            $vars = XConfig::get("Ekom.fishMailerVars");
            $siteName = $vars['site_name'];


            switch ($templateName) {
                case "customer.new":
                    $name = $pool['name'] ?? null;
                    $subject = "Bienvenue sur $siteName";
                    if ($name) {
                        $subject .= ", " . $name;
                    }
                    break;
                case "customer.new.validation":
                    $subject = "Votre inscription sur $siteName";
                    break;
                case "customer.resend_password":
                    $subject = "Réinitialisation de votre mot de passe";
                    break;
                case "order.new":
                    $subject = "Votre commande sur $siteName";
                    break;
                case "order.new.alert":
                    $subject = "Nouvelle commande sur $siteName";
                    break;
                case "estimate.new":
                    $subject = "Votre devis sur $siteName";
                    break;
                case "estimate.new.alert":
                    $subject = "Nouveau devis sur $siteName";
                    break;
                default:
                    $subject = "Informations";
                    break;
            }
            $pool['owner'] = "Ekom";
            $pool['category'] = "all";
            $pool['subject'] = $subject;
            $pool['site_name'] = $siteName;
            $pool['SITE_NAME'] = strtoupper($siteName);
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