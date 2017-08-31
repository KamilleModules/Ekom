<?php


namespace Module\Ekom\Chip\Generated;




class ProductChip
{

    private $reference;
    private $weight;
    private $price;
    private $product_card_id;
    private $product_type;
    private $lang_id;
    private $label;
    private $description;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;
    /**
    * @var ShopProductChip
    */
    private $shop_product;



    public function __construct()
    {
        $this->reference = '';
        $this->weight = 0;
        $this->price = 0;
        $this->product_card_id = 0;
        $this->product_type = NULL;
        $this->lang_id = 0;
        $this->label = '';
        $this->description = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->shop_product = NULL;

    }


    public static function create()
    {
        return new static();
    }

    
    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function getProductCardId()
    {
        return $this->product_card_id;
    }

    public function setProductCardId($product_card_id)
    {
        $this->product_card_id = $product_card_id;
        return $this;
    }

    public function getProductType()
    {
        return $this->product_type;
    }

    public function setProductType($product_type)
    {
        $this->product_type = $product_type;
        return $this;
    }

    public function getLangId()
    {
        return $this->lang_id;
    }

    public function setLangId($lang_id)
    {
        $this->lang_id = $lang_id;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;
        return $this;
    }

    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
        return $this;
    }

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;
        return $this;
    }

    /**
    * @return ShopProductChip
    */
    public function getShopProduct()
    {
        return $this->shop_product;
    }

    public function setShopProduct(ShopProductChip $shop_product)
    {
        $this->shop_product = $shop_product;
        return $this;
    }




}