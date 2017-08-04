<?php


namespace Module\Ekom\Models;


class ProductModel
{

    private $id;
    private $originalPrice;
    private $salePrice;
    private $label;
    private $imageUri;
    private $imagesUri;
    private $taxes;
    private $url;
    private $cardUrl;
    private $reference;
    private $slug;
    private $seller;
    private $card;
    private $categories;
    private $discounts;


    public function __construct()
    {
        $this->id = 0;
        $this->originalPrice = 0;
        $this->salePrice = 0;
        $this->label = "";
        $this->imageUri = "";
        $this->imagesUri = [];
        $this->taxes = [];
        $this->url = "";
        $this->cardUrl = "";
        $this->reference = "";
        $this->slug = "";
        $this->seller = null;
        $this->card = null;
        $this->categories = [];
        $this->discounts = [];
    }

    public static function createFromId($id)
    {
        $o = new static();
        $o->id = $id;
        return $o;
    }


}