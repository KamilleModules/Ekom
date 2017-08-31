<?php


namespace Module\Ekom\Chip\Generated;




class ProductCardChip
{

    private $lang_id;
    private $label;
    private $description;
    private $slug;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;
    /**
    * @var ShopProductCardChip
    */
    private $shop_product_card;
    private $category;
    /**
    * @var ProductChip[]
    */
    private $products;



    public function __construct()
    {
        $this->lang_id = 0;
        $this->label = '';
        $this->description = '';
        $this->slug = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->shop_product_card = NULL;
        $this->category = NULL;
        $this->products = array (
);

    }


    public static function create()
    {
        return new static();
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
    * @return ShopProductCardChip
    */
    public function getShopProductCard()
    {
        return $this->shop_product_card;
    }

    public function setShopProductCard(ShopProductCardChip $shop_product_card)
    {
        $this->shop_product_card = $shop_product_card;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
    * @return ProductChip[]
    */
    public function getProducts()
    {
        return $this->products;
    }

    public function addProduct(ProductChip $product)
    {
        $this->products[] = $product;
        return $this;
    }




}