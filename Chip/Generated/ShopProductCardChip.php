<?php


namespace Module\Ekom\Chip\Generated;




class ShopProductCardChip
{

    private $shop_id;
    /**
    * @var ProductChip
    */
    private $activeProduct;
    private $active;
    private $lang_id;
    private $label;
    private $slug;
    private $description;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;



    public function __construct()
    {
        $this->shop_id = 0;
        $this->activeProduct = NULL;
        $this->active = 0;
        $this->lang_id = 0;
        $this->label = '';
        $this->slug = '';
        $this->description = '';
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

    /**
    * @return ProductChip
    */
    public function getActiveProduct()
    {
        return $this->activeProduct;
    }

    public function setActiveProduct(ProductChip $activeProduct)
    {
        $this->activeProduct = $activeProduct;
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

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
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




}