<?php


namespace Module\Ekom\Models\Product;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Models\Product\AttributeList\AttributeListModel;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


/**
 *
 * //--------------------------------------------
 * // DISCLAIMER
 * //--------------------------------------------
 * This class has been abandoned.
 * It's not because it's a bad idea, but rather because I didn't have the time
 * to implement it.
 * Feel free to continue my work if you think it's useful.
 *
 *
 *
 * Note: prices depend on b2b|b2c
 */
class ProductModel
{

    private $id;

    // product
    private $reference;
    private $weight;
    private $originalPrice;
    private $productCardId;
    private $productTypeId;

    // product lang
    private $label;
    private $description;
    private $metaTitle;
    private $metaDescription;
    private $metaKeywords;


    // shop has product
    private $wholesalePrice;
    private $quantity;
    private $active;
    private $salePrice;
    private $sellerId;


    // shop has product lang
    private $slug;
    private $outOfStockText;


    //
    /**
     * @var AttributeListModel
     */
    private $attributes;

    /**
     * @var array a displayCollection as defined in the ImageLayer.
     */
    private $images;
    private $defaultImage;

    // todo...
    private $taxes;
    private $url;
    private $cardUrl;
    private $categories;
    private $discounts;


    //


    public function __construct()
    {
        $this->id = 0;
        // product
        $this->reference = "";
        $this->weight = 0;
        $this->originalPrice = 0;
        $this->productCardId = 0;
        $this->productTypeId = 0;

        // product lang
        $this->label = "";
        $this->description = "";
        $this->metaTitle = "";
        $this->metaDescription = "";
        $this->metaKeywords = "";

        // shop has product
        $this->wholesalePrice = 0;
        $this->quantity = 0;
        $this->active = 0;
        $this->salePrice = 0;
        $this->sellerId = 0;


        // shop has product lang
        $this->slug = "";
        $this->outOfStockText = "";


        $this->attributes = null;
        $this->images = [];
        $this->defaultImage = "";


        // todo...
        $this->taxes = [];
        $this->url = "";
        $this->cardUrl = "";
        $this->card = null;
        $this->categories = [];
        $this->discounts = [];

        //
        $this->_errors = [];
    }

    public static function createFromId($id, $shopId = null, $langId = null)
    {
        $o = new static();
        $o->id = $id;
        $o->init($id, $shopId, $langId);
        if ('hasError') {
            // send all errors to logs
            return false;
        }
        return $o;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function init($productId, $shopId = null, $langId = null)
    {
        $productId = (int)$productId;
        list($shopId, $langId) = $this->getShopLangIds($shopId, $langId);

        $isB2b = E::isB2b();


        if (false !== ($row = QuickPdo::fetch("
select

p.reference,
p.weight,
p.price as default_price,
p.product_card_id,
p.product_type_id,
 
shpl.label,
shpl.description,
shpl.slug,
shpl.out_of_stock_text,
shpl.meta_title,
shpl.meta_description,
shpl.meta_keywords,

shp.price,
shp.wholesale_price,
shp.quantity,
shp.active,
shp._sale_price_without_tax,
shp._sale_price_with_tax,
shp.seller_id,

l.label as default_label,
l.description as default_description,
l.meta_title as default_meta_title,
l.meta_description as default_meta_description,
l.meta_keywords as default_meta_keywords


from ek_product p
inner join ek_product_lang l on l.product_id=p.id 
inner join ek_shop_has_product shp on shp.product_id=p.id
inner join ek_shop_has_product_lang shpl on shpl.product_id=p.id

 
where p.id=$productId 
and shp.shop_id=$shopId
and l.lang_id=$langId
and shpl.shop_id=$shopId
and shpl.lang_id=$langId

 
"))) {

            // product
            $this->id = $productId;
            $this->reference = $row['reference'];
            $this->weight = $row['weight'];
            $this->originalPrice = (null !== $row['default_price']) ? $row['default_price'] : $row['price'];
            $this->productCardId = $row['product_card_id'];
            $this->productTypeId = $row['product_type_id'];

            // product lang
            $this->label = ('' !== $row['default_label']) ? $row['default_label'] : $row['label'];
            $this->description = ('' !== $row['default_description']) ? $row['default_description'] : $row['description'];
            $this->metaTitle = ('' !== $row['default_meta_title']) ? $row['default_meta_title'] : $row['meta_title'];
            $this->metaDescription = ('' !== $row['default_meta_description']) ? $row['default_meta_description'] : $row['meta_description'];
            $this->metaKeywords = ('' !== $row['default_meta_keywords']) ? $row['default_meta_keywords'] : $row['meta_keywords'];

            // shop has product
            $this->wholesalePrice = $row['wholesale_price'];
            $this->quantity = $row['quantity'];
            $this->active = $row['active'];

            // note: don't forget to sync your app every night... if you use the cached mode
            $this->salePrice = (true === $isB2b) ? $row['_sale_price_without_tax'] : $row['_sale_price_with_tax'];

            $this->sellerId = $row['seller_id'];


            // shop has product lang
            $this->slug = $row['slug'];
            $this->outOfStockText = $row['out_of_stock_text'];


            $this->attributes = AttributeListModel::createByProductId($productId, $langId);


            //--------------------------------------------
            // IMAGES
            //--------------------------------------------
            list($defaultImage, $images) = EkomApi::inst()->imageLayer()->getImagesInfo("product", $productId, true);
            $this->defaultImage = $defaultImage;
            $this->images = $images;


            //--------------------------------------------
            // DISCOUNTS
            //--------------------------------------------


            az($this);


            /**
             * Todo: follow ProductLayer line 431
             */


            // todo...
            $this->taxes = [];
            $this->url = "";
            $this->cardUrl = "";
            $this->card = null;
            $this->categories = [];
            $this->discounts = [];

            //
            $this->_errors = [];


            az($row);

        }


        az("ji");
    }


    private function getShopLangIds($shopId, $langId)
    {
        if (null === $shopId || null === $langId) {
            EkomApi::inst()->initWebContext();
            if (null === $shopId) {
                $shopId = ApplicationRegistry::get("ekom.shop_id");
            }
            if (null === $langId) {
                $langId = ApplicationRegistry::get("ekom.lang_id");
            }
        }
        $shopId = (int)$shopId;
        $langId = (int)$langId;
        return [$shopId, $langId];
    }

}