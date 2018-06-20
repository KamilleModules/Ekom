<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\Invoice;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class InvoiceLayer
{

    public static function getIdByInvoiceNumber(string $invoiceNumber)
    {
        return QuickPdo::fetch("select id from ek_invoice where invoice_number=:ref", [
            "ref" => $invoiceNumber,
        ], \PDO::FETCH_COLUMN);
    }

    public static function getInvoiceIdsByOrderId($orderId)
    {
        $orderId = (int)$orderId;
        return QuickPdo::fetchAll("
select id from ek_invoice where order_id=$orderId
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function getNbInvoicesByUserId($userId)
    {
        $userId = (int)$userId;
        return QuickPdo::fetch("select count(*) as count from ek_invoice where user_id=$userId", [], \PDO::FETCH_COLUMN);
    }


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

    public static function getLastUserInvoice($userId = null, $unserialize = true)
    {
        $userId = E::getUserId($userId);
        $row = QuickPdo::fetch("
select * from ek_invoice where user_id=$userId
order by invoice_date desc         
        ");
        if (false !== $row && true === $unserialize) {
            self::doUnserialize($row);
        }
        return $row;
    }


    public static function getInvoiceById($id, $unserialize = true)
    {
        $id = (int)$id;
        $row = QuickPdo::fetch("select * from ek_invoice where id=$id");
        if (false !== $row && true === $unserialize) {
            self::doUnserialize($row);
        }
        return $row;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function doUnserialize(array &$row)
    {
        $row['user_info'] = unserialize($row['user_info']);
        $row['seller_address'] = unserialize($row['seller_address']);
        $row['shipping_address'] = unserialize($row['shipping_address']);
        $row['billing_address'] = unserialize($row['billing_address']);
        $row['invoice_details'] = unserialize($row['invoice_details']);
    }
}