<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\Api\Layer\InvoiceLayer;
use Module\Ekom\View\Cart\Cartoon\Driver\Driver;
use Module\Ekom\View\Cart\Cartoon\ItemsDescription\ItemsDescriptionRenderer;
use Module\Ekom\View\Cart\Cartoon\TotalRecap\TotalRecapRenderer;
use Module\ThisApp\Ekom\View\Payment\RepaymentScheduleRenderer;

require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";


A::testInit();

$userId = 1;
$invoice = InvoiceLayer::getLastUserInvoice($userId);

$invoiceDetails = $invoice['invoice_details'];
$cartModel = $invoiceDetails['cartModel'];
$paymentDetails = $invoiceDetails['payment_method_details'];
$repaymentSchedule = (array_key_exists("repayment_schedule", $paymentDetails)) ? $paymentDetails['repayment_schedule'] : [];


//--------------------------------------------
// DRIVERS
//--------------------------------------------
$itemsDescriptionDriver = Driver::create()
    ->setTitle("items description")
    ->setPool($_POST)
    ->setPossibleValue2Label([
        'quantity' => "Quantité",
        'image' => "Visuel",
        'reference' => "Référence",
        'seller' => "Vendeur",
        'label' => "Libellé",
        'labelAndDetails' => "Libellé et détails",
        'description' => "Description",
        'originalPrice' => "Prix original",
        'discount' => "Remise",
        'discountLabel' => "Libellé remise",
        'basePrice' => "Prix de base",
        'tax' => "Taxe",
        'taxLabel' => "Libellé taxe",
        'salePrice' => "Prix de vente",
        'linePriceWithoutTax' => "Prix ligne HT",
        'linePriceWithTax' => "Prix ligne TTC",
    ])
    ->setDefaultValues([
        'quantity',
//        'image',
//        'reference',
//        'seller',
//        'label',
        'labelAndDetails',
//        'description',
//        'originalPrice',
//        'discount',
//        'discountLabel',
        'basePrice',
        'tax',
//        'taxLabel',
        'salePrice',
//        'linePriceWithoutTax',
        'linePriceWithTax',
    ]);

$totalRecapDriver = Driver::create()
    ->setTitle("total recap")
    ->setPool($_POST)
    ->setPossibleValue2Label([
        "subtotal" => "Sous-Total HT",
        "shippingCostWithoutTax" => "Frais de livraison HT",
        "shippingTax" => "Taxe frais de livraison",
        "shippingTaxAndDetails" => "Taxe frais de livraison",
        "shippingCostWithTax" => "Frais de livraison TTC",
        "orderTotal" => "Total avant coupons",
        "coupons" => "Coupons",
        "couponsAndDetails" => "Coupons",
        "orderGrandTotal" => "Total",
    ])
    ->setDefaultValues([
        "subtotal",
//            "shippingCostWithoutTax",
//            "shippingTax",
//            "shippingTaxAndDetails",
        "shippingCostWithTax",
        "orderTotal",
        "coupons",
//            "couponsAndDetails",
        "orderGrandTotal",
    ]);


//--------------------------------------------
// GUI
//--------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice tool</title>
    <link rel="stylesheet" href="/css/cartoon.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body class="cartoon-backscreen">

<div class="cartoon-drivers
<?php if (array_key_exists("driver_active", $_POST)): ?>
    active
    <?php endif; ?>
">
    <div class="trigger" id="the-cartoon-driver-trigger">+</div>
    <form class="cartoon-drivers-content" method="post" action="">
        <?php $itemsDescriptionDriver->render(); ?>
        <?php $totalRecapDriver->render(); ?>
    </form>
</div>


<div class="cartoon-invoice invoice-width-1">
    <div class="cartoon-invoice-header">
        <div class="company-header">
            <div class="company-logo">
                <img src="/theme/lee/img/logo-small.png" alt="logo leaderfit">
            </div>
            <div class="company-title">LEADERFIT FORMATION</div>
            <div class="address-line">9 rue du général Mocquery</div>
            <div class="address-line">37550 Saint-Avertin</div>
            <div class="address-line">FRANCE</div>
        </div>
        <div class="invoice-header">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-info-properties">
                <div class="property">
                    <span class="key">Date:</span>
                    <span class="value">2019-05-15</span>
                </div>
                <div class="property">
                    <span class="key">N° de facture:</span>
                    <span class="value">2019780421001</span>
                </div>
                <div class="property">
                    <span class="key">Identifiant leaderfit.com:</span>
                    <span class="value">7ee2019780421001</span>
                </div>
                <div class="property">
                    <span class="key">Montant total:</span>
                    <span class="value">450 €</span>
                </div>
                <div class="property">
                    <span class="key">Méthode de paiement:</span>
                    <span class="value">Carte bleue</span>
                </div>
                <div class="property">
                    <span class="key">Devise:</span>
                    <span class="value">EUR</span>
                </div>
            </div>
        </div>
    </div>

    <div class="payment-certificate-text">
        Cette attestation est la preuve que vous avez bien réalisé l'achat du bon de commande
        décrit sur cette page sur le site leaderfit.com.
    </div>


    <div class="addresses-block">
        <div class="user-billing-address">
            <h4>Facturé à:</h4>
            <div class="address-line">Lafitte Pierre</div>
            <div class="address-line">6 rue port feu hugon</div>
            <div class="address-line">37000 TOURS</div>
            <div class="address-line">lingtalfi@gmail.com</div>
        </div>
    </div>


    <div class="cartoon">
        <?php


        $itemsDescription = ItemsDescriptionRenderer::create()
            ->setItems($cartModel['items'])
            ->setColumns($itemsDescriptionDriver->getChecked());
        $itemsDescription->render();

        ?>

        <div class="total-and-meta-line">
            <div class="order-meta">
                <table>
                    <tr class="nobottomborder">
                        <td class="underline">Suivi des colis</td>
                        <td></td>
                    </tr>
                    <tr class="notopborder">
                        <td class="indent-1">Schenker</td>
                        <td><a href="#">fzfe045grKE88</a></td>
                    </tr>
                    <tr class="nobottomborder">
                        <td class="underline">Points gagnés</td>
                        <td></td>
                    </tr>
                    <tr class="notopborder nobottomborder">
                        <td class="indent-1">Lf-équipement</td>
                        <td>250</td>
                    </tr>
                    <tr class="notopborder">
                        <td class="indent-1">Lf-formation</td>
                        <td>256</td>
                    </tr>
                </table>
            </div>
            <div class="total-recap-container">
                <?php
                $totalRecap = TotalRecapRenderer::create()
                    ->setCartModel($cartModel)
                    ->setRows($totalRecapDriver->getChecked());
                $totalRecap->render();

                ?>
            </div>
        </div>
    </div>
    <div class="repayment-schedule">
        <?php
        RepaymentScheduleRenderer::render($repaymentSchedule);
        ?>
    </div>

    <hr>
    <div class="footer">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci aperiam at commodi corporis cum debitis
        dolore, expedita fugit id ipsa maxime molestiae numquam obcaecati optio, perferendis praesentium provident quae
        voluptates.
    </div>
</div>
<script>
    $(document).ready(function () {
        $("input").on("change", function () {
            var jForm = $(this).closest("form");
            var jCartoonDrivers = jForm.closest(".cartoon-drivers");
            if (jCartoonDrivers.hasClass("active")) {
                jForm.append('<input type="hidden" name="driver_active" value="1">');
            }
            jForm.submit();
            return false;
        });

        $('#the-cartoon-driver-trigger').on('click', function () {
            $(this).parent().toggleClass("active");
        });
    });
</script>
</body>
</html>