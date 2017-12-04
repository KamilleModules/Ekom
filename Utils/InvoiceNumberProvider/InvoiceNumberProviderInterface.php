<?php


namespace Module\Ekom\Utils\InvoiceNumberProvider;


interface InvoiceNumberProviderInterface
{


    /**
     * @param null|string $type ,
     *          the original idea was to provide references for different types of order.
     *          A default order (null), or an estimate order for instance.
     *
     * @return string, an invoice number
     */
    public function getNumber($type = null);
}