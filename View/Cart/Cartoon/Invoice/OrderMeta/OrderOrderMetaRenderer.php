<?php


namespace Module\Ekom\View\Cart\Cartoon\Invoice\OrderMeta;


class OrderOrderMetaRenderer extends OrderMetaRenderer
{


    private $order;

    public function __construct()
    {
        parent::__construct();
        $this->order = [];
    }


    public function setOrder(array $order)
    {
        $this->order = $order;
        return $this;
    }


    protected function getLoyaltyPoints()
    {
        $invoiceDetails = $this->order['order_details'];
        $loyaltyPoints = $invoiceDetails['lfPoints'];
        return $loyaltyPoints;
    }

}