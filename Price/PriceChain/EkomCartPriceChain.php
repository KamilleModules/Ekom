<?php


namespace Module\Ekom\Price\PriceChain;


class EkomCartPriceChain extends PriceChain
{

    private $quantity;

    public function __construct()
    {
        parent::__construct();
        $this->historyBaseName = "_baseCart";
        $this
            ->addNode("salePrice")
            ->addNode("linePrice");


        $this->addTransformer("salePrice", function ($p, array &$model) { // applying quantity

            // the cart contains 4 items of this reference
            return $p * $this->quantity;
        }, "quantity", 300);
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

}