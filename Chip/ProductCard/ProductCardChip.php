<?php


namespace Module\Ekom\Chip\ProductCard;

use Module\Ekom\Chip\Product\ProductChip;
use Module\Ekom\Chip\ShopProductCard\ShopProductCardChip;


/**
 * - ek_product_card
 * - ek_product_card_lang
 */
class ProductCardChip
{

    private $lang_id; // mandatory
    private $label; // mandatory
    private $description;
    private $slug; // mandatory
    private $meta_title;
    private $meta_description;
    private $meta_keywords;

    /**
     * @var ProductChip[]
     */
    private $products;

    /**
     * @var ShopProductCardChip
     */
    private $shopProductCard;
    private $category; // the category name


    public function __construct()
    {
        $this->lang_id = null;
        $this->label = null;
        $this->description = '';
        $this->slug = null;
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->products = [];
        $this->shopProductCard = null;
        $this->category = null;
    }


    public static function create()
    {
        return new static();
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
     * @return null
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
     * @return null
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
     * @return ProductChip[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    public function addProduct(ProductChip $product)
    {
        $this->products[] = $product;
        return $this;
    }

    /**
     * @return ShopProductCardChip
     */
    public function getShopProductCard()
    {
        return $this->shopProductCard;
    }

    public function setShopProductCard(ShopProductCardChip $shopProductCard)
    {
        $this->shopProductCard = $shopProductCard;
        return $this;
    }

    /**
     * @return null
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

}