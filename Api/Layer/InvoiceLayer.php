<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\Invoice;

class InvoiceLayer
{

    /**
     * @param array <invoiceModel>
     * @see EkomModels::invoiceModel()
     * @return int|false, the invoice id
     */
    public static function insert(array $invoice)
    {
        $invoice = Invoice::getDefaults($invoice);
        $invoice['user_info'] = serialize($invoice['user_info']);
        $invoice['seller_address'] = serialize($invoice['seller_address']);
        $invoice['shipping_address'] = serialize($invoice['shipping_address']);
        $invoice['billing_address'] = serialize($invoice['billing_address']);
        $invoice['invoice_details'] = serialize($invoice['invoice_details']);


        $id = EkomApi::inst()->invoice()->create($invoice);
        return $id;
    }
}