<?php


namespace Controller\Ekom\Front\Util;


use Bat\UriTool;
use Core\Services\Hooks;
use Kamille\Architecture\Controller\Web\KamilleController;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Ling\Z;
use Kamille\Services\XLog;
use Kamille\Utils\SessionTransmitter\SessionTransmitter;
use Knp\Snappy\Pdf;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\Exception\EkomUserMessageException;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomRootUser\EkomRootUser;
use Module\Ekom\Utils\Pdf\PdfHtmlInfo;

class PdfController extends KamilleController
{


    /**
     * /download-pdf/invoice?invoice_id=143
     *
     *
     */
    public function download()
    {

        /**
         * Note about passing session
         * -------------------------------
         * wkhtmltopdf will create another session context, and so the connected
         * user session context will be lost by default.
         *
         * Some workarounds I saw on internet pass the sessionId via the params and recreate
         * the session context from there on the target page.
         * I don't feel comfortable with passing the sessionId via the url, as if a malicious
         * user can somehow SEE that sessionId, knowing that the victim is connected,
         * I can imagine (although I don't know how to do it) that this could be exploited.
         *
         * So instead, my workaround, based on a similar idea, is to create a temporary file
         * containing the sessionId, pass the path of the temporary file through the url,
         * and then destroy the temp file immediately when the session context is recreated.
         *
         */

        $file = SessionTransmitter::encapsulateSession();
        $pdfId = Z::getUrlParam("pdfId");
        $fileName = (array_key_exists("filename", $_GET)) ? $_GET['filename'] : "file.pdf";
        $snappy = new Pdf(E::lazyConfig("wkhtmltopdfPath", '/usr/local/bin/wkhtmltopdf'));
        $snappy->setOption("margin-bottom", "10mm");
        $snappy->setOption("footer-html", UriTool::uri("/assets/pdffooter.html", [], true, true));
        $snappy->setTimeout(30);
        $uri = UriTool::uri(E::link("Ekom_pdf", array_replace($_GET, [
            'pdfId' => $pdfId,
        ]), true), array_replace($_GET, [
            "_st" => $file,
        ]));

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $content = $snappy->getOutput($uri);
        return HttpResponse::create($content);

    }


    public function render()
    {
        return $this->doRender();
    }

    public function privateRender()
    {

        if (array_key_exists("user_id", $_GET)) {
            EkomRootUser::connectAs($_GET['user_id']);
        }
        $response = $this->doRender();
        if (array_key_exists("user_id", $_GET)) {
            EkomRootUser::destroyCurrentUser();
        }
        return $response;
    }

    private function doRender()
    {
        /**
         * From zero to hero (wk is alias to wkhtmltopdf)
         * wk --dpi 600  http://lee/pdf/any test.pdf
         * wk -B 10mm --footer-html http://lee/pdfheader.html --dpi 600  http://lee/pdf/any test.pdf && open test.pdf
         *
         *
         *
         */
        try {

            $pdfId = Z::getUrlParam("pdfId");
            $pdfInfo = PdfHtmlInfo::create()->init($pdfId, $_GET);
            Hooks::call("Ekom_PdfHtmlInfo_decorate", $pdfId, $pdfInfo);
            $html = $pdfInfo->getHtml();
            if (empty($html)) {
                throw new \Exception("No module could handle pdfId $pdfId");
            }


        } catch (\Exception $e) {
            if ($e instanceof EkomUserMessageException) {
                $html = $e->getMessage();
            } else {
                $html = E::lazyConfig("pdfErrorMessage", "Une erreur est survenue, veuillez contacter notre service commercial en précisant cette heure: " . date("Y-m-d H:i:s"));
                XLog::error("$e");
            }

        }
        return HttpResponse::create($html);

    }


    public function renderOldTcpdf()
    {
        $pdfId = Z::getUrlParam("pdfId");


        $appDir = Z::appDir();
        require_once($appDir . '/www/TCPDF/tcpdf.php');


        $pdfDir = $appDir . "/pdf";
        $template = $pdfDir . "/hello.tpl.php";
        $fileName = "An example pdf";


        //--------------------------------------------
        //
        //--------------------------------------------
        $userId = 1;
        $invoice = InvoiceLayer::getLastUserInvoice($userId);
        $invoiceDetails = $invoice['invoice_details'];
        $cartModel = $invoiceDetails['cartModel'];
        $paymentDetails = $invoiceDetails['payment_method_details'];
        $repaymentSchedule = (array_key_exists("repayment_schedule", $paymentDetails)) ? $paymentDetails['repayment_schedule'] : [];
        ob_start();
        require_once $template;
        $html = ob_get_clean();


        $this->createPdfByHtml($html, $fileName);
    }


    protected function createPdfByHtml($html, $fileName)
    {

        /**
         * Creates an example PDF TEST document using TCPDF
         * @package com.tecnick.tcpdf
         * @abstract TCPDF - Example: Default Header and Footer
         * @author Nicola Asuni
         * @since 2008-03-04
         */

        // Include the main TCPDF library (search for installation path).

        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator("TCPDF");
        $pdf->SetAuthor('Ekom');
        $pdf->SetTitle($fileName);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);


        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $n = 0;
        $marginLeft = $n;
        $marginTop = $n;
        $marginRight = $n;
        $pdf->SetMargins($marginLeft, $marginTop, $marginRight);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 9, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        // set text shadow effect
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


        $pdf->Output($fileName . '.pdf', 'I');

    }

}