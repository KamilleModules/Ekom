<?php


namespace Module\Ekom\Price\PriceChain;


class EkomTotalPriceChain extends PriceChain
{
    public function __construct()
    {
        parent::__construct();
        $this->historyBaseName = "_baseTotal";
        $this
            ->addNode("linesTotal")
            ->addNode("cartTotal")
            ->addNode("orderSectionSubtotal")
            ->addNode("orderSectionTotal");
    }
}