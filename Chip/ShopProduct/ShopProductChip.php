<?php


namespace Module\Ekom\Chip\ShopProduct;


/**
 *
 * - ek_shop_has_product
 * - ek_shop_has_product_lang
 * - ek_seller
 *
 * This is a ProductChip extension.
 *
 *
 */
class ShopProductChip
{

    private $price;
    private $wholesale_price;
    private $quantity;
    private $active;
    private $_sale_price_without_tax;
    private $_sale_price_with_tax;
    private $seller; // mandatory

    private $label;
    private $description;
    private $slug;
    private $out_of_stock_text;

    private $meta_title;
    private $meta_description;
    private $meta_keywords;


    public function __construct()
    {
        $this->price = NULL;
        $this->wholesale_price = 0;
        $this->quantity = 0;
        $this->active = 1;
        $this->_sale_price_without_tax = 0;
        $this->_sale_price_with_tax = 0;
        $this->seller = null;
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

    /**
     * @return null
     */
    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getWholesalePrice()
    {
        return $this->wholesale_price;
    }

    public function setWholesalePrice($wholesale_price)
    {
        $this->wholesale_price = $wholesale_price;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return int
     */
    public function getSalePriceWithoutTax()
    {
        return $this->_sale_price_without_tax;
    }

    public function setSalePriceWithoutTax($sale_price_without_tax)
    {
        $this->_sale_price_without_tax = $sale_price_without_tax;
        return $this;
    }

    /**
     * @return int
     */
    public function getSalePriceWithTax()
    {
        return $this->_sale_price_with_tax;
    }

    public function setSalePriceWithTax($sale_price_with_tax)
    {
        $this->_sale_price_with_tax = $sale_price_with_tax;
        return $this;
    }

    /**
     * @return null
     */
    public function getSeller()
    {
        return $this->seller;
    }

    public function setSeller($seller)
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutOfStockText()
    {
        return $this->out_of_stock_text;
    }

    public function setOutOfStockText($out_of_stock_text)
    {
        $this->out_of_stock_text = $out_of_stock_text;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
        return $this;
    }

    /**
     * @return string
     */
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