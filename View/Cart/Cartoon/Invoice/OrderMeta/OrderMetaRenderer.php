<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\OrderMeta;


use Module\Ekom\View\Cart\Cartoon\Util\BaseRenderer;

class OrderMetaRenderer extends BaseRenderer
{

    /**
     * @see EkomModels::invoiceModel()
     */
    private $invoice;


    public function __construct()
    {
        parent::__construct();

        $this->columns = [
            'shipping_tracking',
            'lf_points',
        ];
    }

    public function setInvoice(array $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function render()
    {
        $invoiceDetails = $this->invoice['invoice_details'];
        $trackIdentifier = $this->invoice['track_identifier']; // but we need more than just the identifier
        $carrierDetails = $invoiceDetails['carrier_details'];
        $loyaltyPoints = $invoiceDetails['lfPoints'];
        ?>
        <div class="order-meta">
            <table>
                <?php if ($this->has("shipping_tracking")): ?>
                    <tr class="nobottomborder">
                        <td class="underline">Suivi des colis</td>
                        <td></td>
                    </tr>
                    <tr class="notopborder">
                        <td class="indent-1"></td>
                        <td>Le code apparaîtra ici lorsque nous l'aurons récupéré</td>
                    </tr>
                <?php if(false): ?>
                    <tr class="notopborder">
                        <td class="indent-1">Schenker</td>
                        <td><a href="#">demo_code_fzfe045grKE88(todo)</a></td>
                    </tr>
                <?php endif; ?>
                <?php endif; ?>
                <?php
                // this is specific to my company, you should probably remove it
                if ($this->has("lf_points")): ?>
                    <tr class="nobottomborder">
                        <td class="underline">Points gagnés</td>
                        <td></td>
                    </tr>
                    <?php
                    $nb = count($loyaltyPoints);
                    $i = 1;
                    foreach ($loyaltyPoints as $name => $points):
                        $sClass = ($i === $nb) ? "" : "nobottomborder";
                        ?>
                        <tr class="notopborder <?php echo $sClass; ?>">
                            <td class="indent-1"><?php echo $this->getSellerLabel($name); ?></td>
                            <td><?php echo $points; ?></td>
                        </tr>
                        <?php
                        $i++;
                    endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
        <?php
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getSellerLabel($name)
    {
        switch ($name) {
            case "points_equipement":
                return "Lf-équipement";
                break;
            case "points_formation":
                return "Lf-formation";
                break;
            default:
                break;
        }
        return $name;
    }

}