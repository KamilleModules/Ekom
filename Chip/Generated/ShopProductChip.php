<?php


namespace Module\Ekom\Chip\Generated;




class ShopProductChip
{

    private $shop_id;
    private $price;
    private $wholesale_price;
    private $quantity;
    private $active;
    private $_sale_price_without_tax;
    private $_sale_price_with_tax;
    private $seller;
    private $lang_id;
    private $label;
    private $description;
    private $slug;
    private $out_of_stock_text;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;



    public function __construct()
    {
        $this->shop_id = 0;
        $this->price = NULL;
        $this->wholesale_price = 0;
        $this->quantity = 0;
        $this->active = 0;
        $this->_sale_price_without_tax = 0;
        $this->_sale_price_with_tax = 0;
        $this->seller = NULL;
        $this->lang_id = 0;
        $this->label = '';
        $this->description = '';
        $this->slug = '';
        $this->out_of_stock_text = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';

    }


    public static function create()
    {
        return new static();
    }

    
    public function getShopId()
    {
        return $this->shop_id;
    }

    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
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

    public function getWholesalePrice()
    {
        return $this->wholesale_price;
    }

    public function setWholesalePrice($wholesale_price)
    {
        $this->wholesale_price = $wholesale_price;
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    public function getSalePriceWithoutTax()
    {
        return $this->_sale_price_without_tax;
    }

    public function setSalePriceWithoutTax($_sale_price_without_tax)
    {
        $this->_sale_price_without_tax = $_sale_price_without_tax;
        return $this;
    }

    public function getSalePriceWithTax()
    {
        return $this->_sale_price_with_tax;
    }

    public function setSalePriceWithTax($_sale_price_with_tax)
    {
        $this->_sale_price_with_tax = $_sale_price_with_tax;
        return $this;
    }

    public function getSeller()
    {
        return $this->seller;
    }

    public function setSeller($seller)
    {
        $this->seller = $seller;
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

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function getOutOfStockText()
    {
        return $this->out_of_stock_text;
    }

    public function setOutOfStockText($out_of_stock_text)
    {
        $this->out_of_stock_text = $out_of_stock_text;
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




}