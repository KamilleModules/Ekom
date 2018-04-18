<?php


namespace Module\Ekom\Api\Entity;


use Bat\HashTool;
use Bat\StringTool;
use ConditionResolver\SimpleConditionResolverUtil;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\Api\Layer\ProductCodeLayer;
use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Api\Util\UriUtil;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\AttributeSelectorHelper;
use Module\Ekom\Utils\E;

/**
 * @see EkomModels::productBoxModel()
 *
 */
class ProductBoxEntity
{


    private $taxContext;
    private $discountContext;

    /**
     * @var null|array
     */
    private $cacheDeleteIdentifiers;


    private $productCardId;
    private $productId;
    private $productDetails;

    //
    private $_nativeContext;


    public function __construct()
    {
        // hybrid system, see documentation for more info (doc/my/ekom-product-box-implementation.md)
        $this->taxContext = null;
        $this->discountContext = null;
        $this->cacheDeleteIdentifiers = null;
        $this->_nativeContext = null;
    }

    public static function create()
    {
        return new static();
    }

    public function setTaxContext(array $taxContext)
    {
        $this->taxContext = $taxContext;
        return $this;
    }

    public function setDiscountContext(array $discountContext)
    {
        $this->discountContext = $discountContext;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------


    public function setProductCardId($cardId)
    {
        $this->productCardId = (int)$cardId;
        return $this;
    }

    public function setProductId($productId)
    {
        $this->productId = (int)$productId;
        return $this;
    }

    public function setProductDetails(array $productDetails)
    {
        $this->productDetails = $productDetails;
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * @return array, the box model.
     *          Note that this model has two forms and in case of error, the box model contains the following properties:
     *          - errorCode
     *          - errorTitle
     *          - errorMessage
     * @see EkomModels::productBoxModel()
     *
     * @throws EkomException
     */
    public function getModel()
    {
        //--------------------------------------------
        // BASIC CHECKING
        //--------------------------------------------
        $nativeContext = $this->getNativeContext();
        if (null === $nativeContext["product_card_id"]) {
            throw new EkomException("The cardId must be set");
        }


        if (null === $this->taxContext) {
            $this->taxContext = E::getTaxContext();
        }

        if (null === $this->discountContext) {
            $this->discountContext = E::getDiscountContext();
        }


        $productBoxContext = array_replace($nativeContext, $this->taxContext, $this->discountContext);


        $hash = ProductBoxEntityUtil::getHashByCacheContext($productBoxContext);
        $cardId = $nativeContext["product_card_id"];
        $productId = $nativeContext["product_id"];
        $cacheId = "Ekom/figure/productBox-$cardId-$productId--$hash";


        //--------------------------------------------
        // RETURN THE PRODUCT BOX MODEL
        //--------------------------------------------
        return A::cache()->get($cacheId, function () use ($productBoxContext) {

return [];
            $model = [];
            try {


                $isErroneous = false;
                $primitiveModel = $this->getPrimitiveModel($productBoxContext, $isErroneous);

                if (false === $isErroneous) {


                    /**
                     * @todo: remove
                     * You can only subscribe to this hook if you use the tabatha cache ids
                     */
//                Hooks::call("Ekom_decorateBoxModelCachable", $model);
                    /**
                     * Let modules decorate the box model.
                     *
                     * ---------------
                     * !!! THEY MUST BE CONSISTENT WITH THE GIVEN PRODUCT BOX CONTEXT (SHOP ID, LANG ID, ...)
                     * (otherwise, consistency is broken)
                     * ---------------
                     *
                     *
                     *
                     * Reminder: they can do things like:
                     * - change original price (using the priceOriginalRaw property)
                     * - change (stock) quantity
                     * - change taxGroup
                     * @modules: set taxGroup=false to not apply any tax to this product
                     * - change discount
                     * - ...express their intents or other things
                     */
                    Hooks::call("Ekom_decorateBoxModel", $primitiveModel, $productBoxContext);


                    /**
                     * The productDetailsMap is computed from the productDetailsArray, which is only
                     * provided by the Ekom_decorateBoxModel hook.
                     */
                    $primitiveModel['productDetailsMap'] = ProductBoxEntityUtil::getMergedProductDetails($primitiveModel['productDetails']);


                    /**
                     * The product instance can only be computed from productDetailsArray, which are created
                     * from the Ekom_decorateBoxModel hook.
                     */
                    $primitiveModel['uriProductInstance'] = UriUtil::getProductBoxUriByCardSlugProductRef(
                        $primitiveModel['card_slug'],
                        $primitiveModel['product_reference'],
                        $primitiveModel['productDetailsMap']);

                    /**
                     * At this point, the model is considered definitive, especially the
                     * priceOriginal, taxGroup and discount.
                     * So now, we just resolve the priceChain
                     */
                    $model = $primitiveModel;
                    $this->resolvePriceChain($model, $productBoxContext);
                } else {
                    $model = $primitiveModel;
                }
            } catch (\Exception $e) {
                $productId = $productBoxContext["product_id"];
                $cardId = (int)$productBoxContext["product_card_id"];
                $model['errorCode'] = "exception";
                $model['errorTitle'] = "Exception occurred";
                $model['errorMessage'] = $e->getMessage() . "($cardId, $productId)";
                $model['errorTrace'] = $e->getTraceAsString();

            }


            ksort($model);
//            az(__FILE__, $model);
            return $model;


        });
    }





    //--------------------------------------------
    //
    //--------------------------------------------

    private function resolvePriceChain(array &$model, $productBoxContext)
    {

        $rawOriginalPrice = E::trimPrice($model['priceOriginalRaw']); // ensure that modules didn't parasite us with some lengthy floats
        $taxGroup = $model['taxGroup'];
        $discount = $model['discount'];

        /**
         * Any data that can be computed only using the 3 variables above should be defined inside
         * this method.
         *
         * The following properties correspond to this criteria:
         *
         *
         * // related to price
         * ------------------------
         * - priceOriginalRaw (already set in the given model)
         * - priceOriginal
         * - priceBase (discounts applied on original price)
         * - priceBaseRaw
         * - priceSale (applicable taxes applied on base price)
         * - priceSaleRaw
         *
         *
         * // related to discount
         * ------------------------
         * // Note: the discount price is the priceSale
         * // Note2: badgeDetails has been removed for now
         *
         * - discountHasDiscount: bool
         * - discountType: the procedure type (percent|fixed)
         * - discountSavingPercent
         * - discountSavingFixed
         * - discountRawSavingFixed
         * - discountLabel
         *
         *
         *
         *
         * // related to taxes
         * ------------------------
         * - taxAmount
         * - taxGroupLabel
         * - taxGroupName
         * - taxHasTax
         * - taxRatio
         *
         *
         *
         *
         */
        //--------------------------------------------
        // APPLYING DISCOUNTS
        //--------------------------------------------
        $discountInfo = [];
        if (false !== $discount) {
            $discountContext = $productBoxContext;
//            $discountContext['datetime'] = date("Y-m-d H:i:s");
            $conditions = $discount['conditions'];
            if (true === SimpleConditionResolverUtil::create()->evaluate($conditions, $discountContext)) {
                $discountInfo = DiscountLayer::applyDiscount($discount, $rawOriginalPrice);
                $discountInfo['label'] = $discount['label'];
            }
        }
        if (!$discountInfo) {
            $discountInfo = [
                "type" => "fixed",
                "savingPercent" => 0,
                "savingFixed" => 0,
                "discountPrice" => $rawOriginalPrice,
                "label" => "",
            ];
        }
        $discountPrice = $discountInfo['discountPrice'];

        $model['discountHasDiscount'] = ($discountInfo['savingFixed'] > 0);
        $model['discountLabel'] = $discountInfo['label'];
        $model['discountType'] = $discountInfo['type'];
        $model['discountPrice'] = E::price($discountPrice);
        $model['discountRawPrice'] = $discountPrice;
        $model['discountSavingPercent'] = $discountInfo['savingPercent'];
        $model['discountSavingFixed'] = E::price($discountInfo['savingFixed']);
        $model['discountRawSavingFixed'] = $discountInfo['savingFixed'];
        $basePrice = $discountPrice;


        //--------------------------------------------
        // APPLYING TAXES
        //--------------------------------------------
        $taxInfo = TaxLayer::applyTaxGroup($taxGroup, $basePrice);
        /**
         * Note that if the taxGroup was refuted by modules (=false),
         * the base price used by ekom is still the taxInfo.priceWithTax (which happens to be the
         * same as the taxInfo.priceWithoutTax)
         */
        $salePrice = $taxInfo['priceWithTax'];


//        $model["taxDetails"] = $taxInfo['taxDetails'];
        $model["taxRatio"] = $taxInfo['taxRatio'];
        $model["taxGroupName"] = $taxInfo['taxGroupName'];
        $model["taxGroupLabel"] = $taxInfo['taxGroupLabel'];
        $model["taxAmount"] = $taxInfo['taxAmountUnit'];
        $model["taxHasTax"] = ($taxInfo['taxAmountUnit'] > 0); // whether or not the tax was applied


        //--------------------------------------------
        // INCLUDING PRICE CHAIN
        //--------------------------------------------
        $model['priceOriginal'] = E::price($rawOriginalPrice);
        $model['priceBaseRaw'] = $basePrice;
        $model['priceBase'] = E::price($basePrice);
        $model['priceSaleRaw'] = $salePrice;
        $model['priceSale'] = E::price($salePrice);
    }

    private function getPrimitiveModel(array $productBoxContext, &$isErroneous = false)
    {

        $model = [];

        //
        $productId = $productBoxContext["product_id"];
        $cardId = (int)$productBoxContext["product_card_id"];
        $productDetails = $productBoxContext["product_details"];


        if (false !== ($row = ProductBoxEntityUtil::getProductCardInfoByCardId($cardId))) {

            if ('1' === $row['active']) {

                $api = EkomApi::inst();




                /**
                 * Take the list of attributes
                 */
                $productsInfo = ProductBoxEntityUtil::getProductCardProductsWithAttributes($cardId);

                if (count($productsInfo) > 0) {


                    $productId = (string)$productId;
                    if (empty($productId)) {
                        $productId = $row['product_id'];
                        if (null === $productId) {
                            $productId = $productsInfo[0]["product_id"];
                        }
                    }


                    //--------------------------------------------
                    // IMAGES
                    //--------------------------------------------
                    list($defaultImage, $images) = $api->imageLayer()->getImagesInfo("productBox", [
                        $productId,
                        $cardId,
                    ], true);

                    $imageThumb = "";
                    $imageSmall = "";
                    $imageMedium = "";
                    $imageLarge = "";
                    if ($defaultImage) {
                        $imageThumb = $images[$defaultImage]['thumb'];
                        $imageSmall = $images[$defaultImage]['small'];
                        $imageMedium = $images[$defaultImage]['medium'];
                        $imageLarge = $images[$defaultImage]['large'];
                    }


                    //--------------------------------------------
                    // CHOSEN PRODUCT (main product)
                    //--------------------------------------------
                    $p = null;
                    if (null !== $productId) {
                        foreach ($productsInfo as $pr) {
                            if ($productId === $pr['product_id']) {
                                $p = $pr;
                                break;
                            }
                        }
                        if (null === $p) {
                            throw new EkomApiException("the default product_id $productId was not found in card $cardId");
                        }
                    }
                    if (null === $p) {
                        $p = $productsInfo[0];
                    }


                    $label = ('' !== $p['label']) ? $p['label'] : $row['label'];
                    $description = ('' !== $p['description']) ? $p['description'] : $row['description'];


                    //--------------------------------------------
                    // BASE INFO
                    //--------------------------------------------
                    $productReference = $p['reference'];
                    $cardSlug = $row['slug'];

                    $cardUri = E::link("Ekom_productCardRef", [
                        'slug' => $cardSlug,
                        'ref' => $productReference,
                    ]);


                    //--------------------------------------------
                    // META
                    //--------------------------------------------
                    $metaTitle = ('' !== $p['meta_title']) ? $p['meta_title'] : $row['meta_title'];
                    $metaDescription = ('' !== $p['meta_description']) ? $p['meta_description'] : $row['meta_description'];
                    $metaKeywords = ('' !== $p['meta_keywords']) ? $p['meta_keywords'] : $row['meta_keywords'];


                    //--------------------------------------------
                    // STOCK
                    //--------------------------------------------
                    $outOfStockText = $p['out_of_stock_text'];
                    $quantity = (int)$p['quantity'];
                    $isInStock = ($quantity > 0 || -1 === $quantity);

                    //--------------------------------------------
                    // ATTRIBUTES
                    //--------------------------------------------
                    $attr = AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($productsInfo, $productId);


                    $attrSelection = $p['attributes'];
                    $attrStringArr = [];
                    foreach ($attrSelection as $item) {
                        $attrStringArr[] = $item['value_label'];
                    }
                    $attrString = implode(' | ', $attrStringArr);


                    //--------------------------------------------
                    // RATING
                    //--------------------------------------------
                    $ratingInfo = $api->commentLayer()->getRatingInfo($cardId);


                    //--------------------------------------------
                    // CODES
                    //--------------------------------------------
                    /**
                     * Abstract codes for things like:
                     *      - is new product
                     *      - ...
                     */
                    $codes = ProductCodeLayer::extractCodes($p['codes']);


                    //--------------------------------------------
                    // TAXES AND BASE PRICE
                    //--------------------------------------------
                    $taxRuleId=  $row['tax_rule_id'];
                    $taxGroup = TaxLayer::getTaxGroupModelByTaxRuleId($taxRuleId, $this->taxContext);


                    //--------------------------------------------
                    // DISCOUNT
                    //--------------------------------------------
                    $discount = $api->discountLayer()->getApplicableDiscountByProductId($p['product_id'], $this->discountContext);
                    /**
                     * Reminder: for now, only one discount per product is applied.
                     */
                    $discountBadge = "";
                    if (is_array($discount)) {
                        $discountBadge = DiscountLayer::getBadge($discount);
                    }


                    $model = [
                        //--------------------------------------------
                        // BASIC DATA
                        //--------------------------------------------
                        "card_id" => (int)$cardId,
                        "card_slug" => $cardSlug,
                        "product_id" => (int)$productId,
                        "product_reference" => $productReference,
                        "product_type" => $p['product_card_type_name'],
                        "product_type_label" => $p['product_card_type_label'],
                        "product_type_id" => $p['product_card_type_id'],
                        "quantityStock" => (int)$quantity,
                        "quantityInStock" => $isInStock,
                        "images" => $images,
                        "defaultImage" => $defaultImage,
                        "imageThumb" => $imageThumb,
                        "imageSmall" => $imageSmall,
                        "imageMedium" => $imageMedium,
                        "imageLarge" => $imageLarge,

                        "uriCard" => $cardUri,
                        "uriCardAjax" => UriUtil::getProductBoxBaseAjaxUri($productId),
                        "uriProduct" => UriUtil::getProductBoxUriByCardSlugProductRef($cardSlug, $productReference),

                        "label" => $label,
                        "label_flat" => strtolower(StringTool::removeAccents($label)), // use this for dynamic sorting
                        "seller" => $p['seller'],
                        "seller_label" => $p['seller_label'],


                        "ref" => $p['reference'],
                        "wholesale_price" => $p['wholesale_price'],
                        "weight" => $p['weight'],
                        "description" => $description,
                        //
                        "metaTitle" => $metaTitle,
                        "metaDescription" => $metaDescription,
                        "metaKeywords" => $metaKeywords,
                        /**
                         * Is used by the widget to assign visual cues (for instance success color) to the stockText
                         * List of available types will be defined later.
                         */
                        "outOfStockText" => $outOfStockText,
                        //
                        "attributesString" => $attrString,
                        "attributesSelection" => $attrSelection,
                        "attributes" => $attr,

                        // rating & popularity
                        "rating_amount" => $ratingInfo['average'], // percent
                        "rating_nbVotes" => $ratingInfo['count'],
                        "popularity" => $p['_popularity'],

                        // codes
                        "codes" => $codes,
                        //--------------------------------------------
                        // PRICE RELATED
                        //--------------------------------------------
                        "priceOriginalRaw" => $p['price'], // the original price (price from the shop_has_product table, or, if null, from product table)
                        // tax
                        "taxGroup" => $taxGroup, // false|array
                        // discount
                        "discount" => $discount, // false|array
                        "discountBadge" => $discountBadge, // string

                        /**
                         * The product details array (major/minor), yet to be created by modules
                         */
                        "productDetails" => [],
                        /**
                         * The product details identifying the product.
                         * This will be computed from productDetails by this class in a moment.
                         */
                        "productDetailsMap" => [],
                        "productDetailsArgs" => $productDetails,  // the product details from the uri if any
                        /**
                         * The product details representing the current state of the box.
                         * This must be fed by modules, each item contains the following:
                         * - name
                         * - attribute_label
                         * - value
                         * - value_label
                         */
                        "productDetailsSelection" => [],

                        // for dev/debug
                        "productBoxContext" => $productBoxContext,
                    ];


                } else {
                    $model['errorCode'] = "emptyProductCard";
                    $model['errorTitle'] = "Empty product card";
                    $model['errorMessage'] = "This product card does not contain any products ($cardId, $productId)";
                }
            } else {
                /**
                 * product card not associated with this shop/lang.
                 */
                $model['errorCode'] = "inactive";
                $model['errorTitle'] = "Product card not active";
                $model['errorMessage'] = "This product card is not active for this shop, sorry ($cardId, $productId)";
            }
        } else {
            /**
             * product card not associated with this shop/lang.
             */
            $model['errorCode'] = "noAssociation";
            $model['errorTitle'] = "Product card not associated";
            $model['errorMessage'] = "This product card is not associated with this shop, sorry (cardId: $cardId)";
        }

        $isErroneous = (array_key_exists('errorCode', $model));
        return $model;
    }


    private function getMetaTitle(array $product, array $card, $label)
    {
        $ret = '';

        if ('' !== $product['meta_title']) { // ek_shop_has_product_lang.description
            $ret = $product['meta_title'];
        } elseif ('' !== $product['default_meta_title']) { // ek_product_lang.description
            $ret = $product['default_meta_title'];
        } else {
            // ek_shop_has_product_card_lang.description and ek_product_card_lang.description
            $ret = ("" !== $card['meta_title']) ? $card['meta_title'] : $card['default_meta_title'];
        }
        if ('' === $ret) {
            // ekom heuristics
            $ret = $label . ' (' . $product['reference'] . ')';

        }
        return $ret;
    }

    private function getMetaDescription(array $product, array $card, $label, $description)
    {
        $ret = '';

        if ('' !== $product['meta_description']) { // ek_shop_has_product_lang.description
            $ret = $product['meta_description'];
        } elseif ('' !== $product['default_meta_description']) { // ek_product_lang.description
            $ret = $product['default_meta_description'];
        } else {
            // ek_shop_has_product_card_lang.description and ek_product_card_lang.description
            $ret = ("" !== $card['meta_description']) ? $card['meta_description'] : $card['default_meta_description'];
        }
        if ('' === $ret) {
            // ekom heuristics
            $ret = $description;

        }
        return $ret;
    }

    private function getMetaKeywords(array $product, array $card, $label, $description)
    {
        $ret = [];

        if ('' !== $product['meta_keywords']) { // ek_shop_has_product_lang.description
            $ret = unserialize($product['meta_keywords']);
        } elseif ('' !== $product['default_meta_keywords']) { // ek_product_lang.description
            $ret = unserialize($product['default_meta_keywords']);
        } else {
            // ek_shop_has_product_card_lang.description and ek_product_card_lang.description
            $ret = ("" !== $card['meta_keywords']) ? unserialize($card['meta_keywords']) : unserialize($card['default_meta_keywords']);
        }
        if ('' === $ret) {
            // ekom heuristics
            $ret = [];
        }
        return $ret;
    }

    private function getNativeContext()
    {
        if (null === $this->_nativeContext) {
            $productCardId = $this->productCardId;
            $productId = $this->productId;
            $productDetails = $this->productDetails;


            if (null === $productDetails) {
                $productDetails = [];
            }

            $this->_nativeContext = [
                'product_card_id' => $productCardId,
                'product_id' => $productId,
                'product_details' => $productDetails,
            ];

        }
        return $this->_nativeContext;
    }
}








































