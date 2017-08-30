<?php


namespace Module\Ekom\Chip\ShopProductCard;


/**
 *
 * - ek_shop_has_product_card
 * - ek_shop_has_product_card_lang
 *
 *
 * This is an extension for the ProductCardShip.
 *
 */
class ShopProductCardChip
{

    private $shopId; // mandatory
    private $active;
    /**
     * the activeRef helps the processor to define the
     * ek_shop_has_product_card.active field.
     * Note: it could be defined by other means (activeProductId, activeProductChip...)
     * if we wanted to.
     */
    private $activeRef; // mandatory
    private $label;
    private $slug;
    private $description;
    private $meta_title;
    private $meta_description;
    private $meta_keywords;


    public function __construct()
    {
        $this->active = 0;
        $this->shopId = null;
        $this->activeRef = null;
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
     * @return mixed
     */
    public function getActiveRef()
    {
        return $this->activeRef;
    }

    public function setActiveRef($activeRef)
    {
        $this->activeRef = $activeRef;
        return $this;
    }

    /**
     * @return null
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        return $this;
    }


}