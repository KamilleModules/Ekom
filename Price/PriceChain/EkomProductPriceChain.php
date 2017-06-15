<?php


namespace Module\Ekom\Price\PriceChain;


use Module\Ekom\Api\EkomApi;

class EkomProductPriceChain extends PriceChain
{

    private $cardId;
    private $shopId;
    private $langId;
    private $usePriceWithTax;


    public function __construct()
    {
        parent::__construct();
        $this->historyBaseName = "_baseProduct";
        $this
            ->addNode("price")
            ->addNode("salePrice");


        $taxLayer = EkomApi::inst()->taxLayer();

        $this->addTransformer("price", function ($p, array &$model) use ($taxLayer) { // applying taxes (b2b model)

            // get taxes
            $taxes = $taxLayer->getTaxesByCardId($this->cardId, $this->shopId, $this->langId);
            $taxDetails = [];
            $priceWithTax = $taxLayer->applyTaxesToPrice($taxes, $p, $taxDetails);
            $model['rawPriceWithoutTax'] = $p;
            $model['rawPriceWithTax'] = $priceWithTax;
            $model['taxDetails'] = $taxDetails;


            if (true === $this->usePriceWithTax) {
                return $priceWithTax;
            }
            return $p;


        }, "taxB2CMode", 100);


        $this->addTransformer("price", function ($p, array &$model) { // applying discounts

            $model['discountsDetails'] = [
                "-3€" => "discounts details here...",
            ];

            return $p - 3; // -3€
        }, "discount", 200);


    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
        return $this;
    }

    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        return $this;
    }

    public function setLangId($langId)
    {
        $this->langId = $langId;
        return $this;
    }

    public function setUsePriceWithTax($usePriceWithTax)
    {
        $this->usePriceWithTax = (bool)$usePriceWithTax;
        return $this;
    }
}