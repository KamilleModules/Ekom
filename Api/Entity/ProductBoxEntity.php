<?php


namespace Module\Ekom\Api\Entity;


use Bat\HashTool;
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

class ProductBoxEntity
{
    /**
     * @var null|array
     */
    private $generalContext;
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
        $this->generalContext = null;
        $this->cacheDeleteIdentifiers = null;
        $this->_nativeContext = null;
    }

    public static function create()
    {
        return new static();
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function setGeneralContextValue($key, $value)
    {
        if (null === $this->generalContext) {
            $this->generalContext = [];
        }
        $this->generalContext[$key] = $value;
        return $this;
    }

    public function setGeneralContext(array $context)
    {
        $this->generalContext = $context;
        return $this;
    }

    public function getGeneralContext()
    {
        if (null === $this->generalContext) {
            $this->generalContext = [];
        }
        return $this->generalContext;
    }


    public function getNativeContext()
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


    public function setProductCardId($cardId)
    {
        $this->productCardId = $cardId;
        return $this;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
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


        //--------------------------------------------
        // PREPARING THE CACHE IDENTIFIERS
        //--------------------------------------------
        $cacheIdentifiers = self::getCacheDeleteIdentifiers();


        $generalContext = $this->generalContext;


        /**
         * If the developer didn't set the generalContext manually, we create it automatically.
         */
//        az(__FILE__, $generalContext);
        if (null === $generalContext) {
            $generalContext = ProductBoxEntityUtil::getProductBoxGeneralContext();
        }


        $productBoxContext = array_replace($nativeContext, $generalContext);
        $hash = ProductBoxEntityUtil::getHashByCacheContext($productBoxContext);
        $hashString = "ekom-pbox-$hash";


        //--------------------------------------------
        // RETURN THE PRODUCT BOX MODEL
        //--------------------------------------------
        return A::cache()->get($hashString, function () use ($productBoxContext) {


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
                     * Reminder: they can do things like:
                     * - change original price (using the priceOriginalRaw property)
                     * - change (stock) quantity
                     * - change taxGroup
                     *          @modules: set taxGroup=false to not apply any tax to this product
                     * - change discount
                     * - ...express their intents or other things
                     */
                    Hooks::call("Ekom_decorateBoxModel", $primitiveModel, $productBoxContext);


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
            return $model;


        }, $cacheIdentifiers);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public static function getCacheDeleteIdentifiers()
    {
        $nativeDeleteIdentifiers = self::getDeleteIdentifiers();
        $modulesDeleteIdentifiers = [];
        Hooks::call("Ekom_ProductBox_getTabathaDeleteIdentifiers", $modulesDeleteIdentifiers);
        return array_unique(array_merge($nativeDeleteIdentifiers, $modulesDeleteIdentifiers));
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
         * - priceBase (assumed with applicable tax applied)
         * - priceBaseRaw
         * - priceSale (assumed with applicable tax and discount applied)
         * - priceSaleRaw
         *
         *
         * @deprecated (as not useful for now)
         * - basePriceWithoutTax
         * - rawBasePriceWithoutTax
         * - salePriceWithoutTax (who needs that?)
         * - rawSalePriceWithoutTax
         *
         *
         *
         * // related to taxes
         * ------------------------
         * - taxDetails
         * - taxRatio
         * - taxGroupName
         * - taxGroupLabel
         * - taxAmountUnit
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
         *
         *
         */
        //--------------------------------------------
        // APPLYING TAXES
        //--------------------------------------------
        $taxInfo = TaxLayer::applyTaxGroup($taxGroup, $rawOriginalPrice);
        /**
         * Note that if the taxGroup was refuted by modules (=false),
         * the base price used by ekom is still the taxInfo.priceWithTax (which happens to be the
         * same as the taxInfo.priceWithoutTax)
         */
        $basePrice = $taxInfo['priceWithTax'];
        $basePriceWithoutTax = $taxInfo['priceWithoutTax'];


        $model["taxDetails"] = $taxInfo['taxDetails'];
        $model["taxRatio"] = $taxInfo['taxRatio'];
        $model["taxGroupName"] = $taxInfo['taxGroupName'];
        $model["taxGroupLabel"] = $taxInfo['taxGroupLabel'];
        $model["taxAmountUnit"] = $taxInfo['taxAmountUnit'];
        $model["taxHasTax"] = ($taxInfo['taxAmountUnit'] > 0); // whether or not the tax was applied


        //--------------------------------------------
        // NOW APPLYING DISCOUNT
        //--------------------------------------------
        $discountInfo = [];
        if (false !== $discount) {
            $conditions = $discount['conditions'];
            if (true === SimpleConditionResolverUtil::create()->evaluate($conditions, $productBoxContext)) {
                $discountInfo = DiscountLayer::applyDiscount($discount, $basePrice);
            }
        }
        if (!$discountInfo) {
            $discountInfo = [
                "type" => "fixed",
                "savingPercent" => 0,
                "savingFixed" => 0,
                "discountPrice" => $basePrice,
            ];
        }
        $discountPrice = $discountInfo['discountPrice'];

        $model['discountHasDiscount'] = ($discountInfo['savingFixed'] > 0);
        $model['discountType'] = $discountInfo['type'];
        $model['discountPrice'] = E::price($discountPrice);
        $model['discountRawPrice'] = $discountPrice;
        $model['discountSavingPercent'] = $discountInfo['savingPercent'];
        $model['discountSavingFixed'] = E::price($discountInfo['savingFixed']);
        $model['discountRawSavingFixed'] = $discountInfo['savingFixed'];


        //--------------------------------------------
        // INCLUDING PRICE CHAIN
        //--------------------------------------------
        $salePrice = $discountPrice;
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
        $shopId = (int)$productBoxContext["shop_id"];
        $langId = (int)$productBoxContext["lang_id"];
        $productDetails = $productBoxContext["product_details"];


        if (false !== ($row = ProductBoxEntityUtil::getProductCardInfoByCardId($cardId, $shopId, $langId))) {
            if ('1' === $row['active']) {

                $api = EkomApi::inst();

                /**
                 * Take the list of attributes
                 */
                $productsInfo = ProductBoxEntityUtil::getProductCardProductsWithAttributes($cardId, $shopId, $langId);

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


                    if ('' !== $p['label']) {
                        $label = $p['label'];
                    } elseif ("" !== $row['label']) {
                        $label = $row['label'];
                    } else {
                        $label = ("" !== $p['default_label']) ? $p['default_label'] : $row['default_label'];
                    }

                    if ('' !== $p['description']) {
                        $description = $p['description'];
                    } elseif ("" !== $row['description']) {
                        $description = $row['description'];
                    } else {
                        $description = ("" !== $p['default_description']) ? $p['default_description'] : $row['default_description'];
                    }


                    //--------------------------------------------
                    // BASE INFO
                    //--------------------------------------------
                    $productReference = $p['reference'];
                    $cardSlug = ("" !== $row['slug']) ? $row['slug'] : $row['default_slug'];
                    $cardUri = E::link("Ekom_productCardRef", [
                        'slug' => $cardSlug,
                        'ref' => $productReference,
                    ]);


                    //--------------------------------------------
                    // META
                    //--------------------------------------------
                    $metaTitle = $this->getMetaTitle($p, $row, $label);
                    $metaDescription = $this->getMetaDescription($p, $row, $label, $description);
                    $metaKeywords = $this->getMetaKeywords($p, $row, $label, $description);


                    //--------------------------------------------
                    // STOCK
                    //--------------------------------------------
                    $outOfStockText = $p['out_of_stock_text'];
                    $quantity = $p['quantity'];
                    $isInStock = true; // isInStock handles qty=-1, it's a helper for the view
                    if (0 === (int)$quantity) {
                        $isInStock = false;
                    }

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
                    $taxGroup = TaxLayer::getTaxGroupInfoByCardId($cardId, $shopId, $langId);


                    //--------------------------------------------
                    // DISCOUNT
                    //--------------------------------------------
                    $discount = $api->discountLayer()->getApplicableDiscountByProductId($p['product_id'], $shopId, $langId);

                    $model = [
                        //--------------------------------------------
                        // BASIC DATA
                        //--------------------------------------------
                        "card_id" => (int)$cardId,
                        "card_slug" => $cardSlug,
                        "product_id" => (int)$productId,
                        "product_reference" => $productReference,
                        "product_type" => $p['product_type'],
                        "quantity" => (int)$quantity,
                        "is_in_stock" => $isInStock,
                        "images" => $images,
                        "defaultImage" => $defaultImage,
                        "imageThumb" => $imageThumb,
                        "imageSmall" => $imageSmall,
                        "imageMedium" => $imageMedium,
                        "imageLarge" => $imageLarge,

                        "uriCard" => $cardUri,
                        "uriCardAjax" => UriUtil::getProductBoxBaseAjaxUri($productId),
                        "label" => $label,
                        "seller" => $p['seller'],


                        "label_escaped" => htmlspecialchars($label),
                        "ref" => $p['reference'],
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
                        // rating
                        "rating_amount" => $ratingInfo['average'], // percent
                        "rating_nbVotes" => $ratingInfo['count'],

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

                        /**
                         * will be decorated by modules if present.
                         * Format is defined in ekom doc:
                         * - major: array of key => value
                         * - minor: array of key => value
                         */
                        "productDetails" => [],
                        "productDetailsArgs" => $productDetails,  // the product details from the uri if any
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
            $model['errorMessage'] = "This product card is not associated with this shop, sorry (cardId: $cardId, shopId: $shopId, langId: $langId)";
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


    private static function getDeleteIdentifiers()
    {


        return [


            "ek_shop_has_product_card_lang",
            "ek_shop_has_product_card",
            "ek_product_card_lang",
            "ek_product_card",
            "ek_shop",
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ek_product.delete",
            "ek_product.update",
            "ekomApi.image.product",
            "ekomApi.image.productCard",

            // taxes
            "ek_tax",
            "ek_tax_group_has_tax",
            "ek_tax_group",
            "ek_product_card_has_tax_group",


            // discounts
            "ek_product",
            'ek_discount',
            'ek_discount_lang',
            'ek_product_has_discount',
            'ek_product_card_has_discount',
            'ek_category_has_discount',
            'ek_category.delete',
            'ek_product_card.delete',
            "ek_user_has_user_group",
        ];
    }
}








































