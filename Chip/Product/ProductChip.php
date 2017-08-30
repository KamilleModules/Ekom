<?php


namespace Module\Ekom\Chip\Product;

use Module\Ekom\Chip\ShopProduct\ShopProductChip;


/**
 *
 * - ek_product
 * - ek_product_lang
 * - ek_product_type
 *
 */
class ProductChip
{

    private $reference; // mandatory
    private $weight;
    private $price;
    private $type; // mandatory
    private $shop_id; // mandatory

    private $lang_id; // mandatory
    private $label; // mandatory
    private $description;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;

    private $shopProduct;

    public function __construct()
    {
        $this->reference = null;
        $this->weight = 0;
        $this->price = 0;
        $this->type = null;
        $this->lang_id = null;
        $this->shop_id = null;
        $this->label = '';
        $this->description = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->shopProduct = null;
    }


    public static function create()
    {
        return new static();
    }


    /**
     * @return null
     */
    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return int
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
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return null
     */
    public function getLangId()
    {
        return $this->lang_id;
    }

    public function setLangId($lang_id)
    {
        $this->lang_id = $lang_id;
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

    /**
     * @return null
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
        return $this;
    }

    /**
     * @return ShopProductChip
     */
    public function getShopProduct()
    {
        return $this->shopProduct;
    }

    public function setShopProduct(ShopProductChip $shopProduct)
    {
        $this->shopProduct = $shopProduct;
        return $this;
    }


}