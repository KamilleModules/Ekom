<?php


namespace Module\Ekom\Chip\ProductCard;

use Module\Ekom\Api\EkomApi;
use Module\Ekom\Chip\ChipConfig;
use Module\Ekom\Chip\Exception\ChipException;


/**
 *
 *
 *
 *
 * - ek_product_card
 * - ek_product_card_lang
 * - ek_shop_has_product_card
 * - ek_shop_has_product_card_lang
 * - ?ek_category_has_product_card
 */
class ProductCardChip
{

    /**
     * @todo-ling: the ek_shop_has_product_card table REQUIRES a product!!
     */
    private $shopId; // mandatory
    private $langId; // mandatory
    private $label; // mandatory
    private $description;
    private $slug; // mandatory
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;
    private $categoryId;
    private $active; // bool=true: active for this shop?

    /**
     * @var ProductChip[]
     */
    private $products;


    public function __construct()
    {
        $this->langId = ChipConfig::DEFAULT_LANG_ISO;
        $this->active = true;
    }


    public static function create()
    {
        return new static();
    }

    /**
     * @return mixed
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

    /**
     * @return string
     */
    public function getLangId()
    {
        return $this->langId;
    }

    public function setLangId($langId)
    {
        $this->langId = $langId;
        return $this;
    }

    public function setLangIdByIso($iso)
    {
        $this->langId = EkomApi::inst()->langLayer()->getLangIdByIso($iso);
        if (false === $this->langId) {
            throw new ChipException("langId not found with iso $iso");
        }
        return $this;
    }

    /**
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function setCategoryIdByName($categoryName)
    {
        $this->categoryId = EkomApi::inst()->categoryLayer()->getCategoryIdByName($categoryName);
        if (false === $this->categoryId) {
            throw new ChipException("category not found with name $categoryName");
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }



}