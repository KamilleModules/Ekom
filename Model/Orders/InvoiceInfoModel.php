<?php


namespace Module\Ekom\Model\Orders;


use Module\Ekom\Api\Layer\InvoiceLayer;
use QuickPdo\QuickPdo;

class InvoiceInfoModel extends OrderInfoModel
{


    public static function getModelByOrderId($id)
    {
        $invoices = [];
        $id = (int)$id;
        $invoiceIds = QuickPdo::fetchAll("select id from ek_invoice where order_id=$id", [], \PDO::FETCH_COLUMN);
        foreach ($invoiceIds as $invoiceId) {
            $invoices[$invoiceId] = InvoiceLayer::getInvoiceById($invoiceId);
        }
        return $invoices;
    }
}