<?php


namespace Module\Ekom\Api\Entity;


use Bat\HashTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
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
    private $modulesContextData;
    /**
     * @var null|array
     */
    private $cacheDeleteIdentifiers;


    private $shopId;
    private $langId;
    private $productCardId;
    private $productId;
    private $productDetails;
    private $date;
    private $groupIds;
    private $currencyId;

    // cache
    private $_nativeContextData;


    public function __construct()
    {

        // hybrid system, see documentation for more info (doc/my/ekom-product-box-implementation.md)
        $this->modulesContextData = null;
        $this->cacheDeleteIdentifiers = null;
    }

    public static function create()
    {
        return new static();
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function setModulesContextDataValue($key, $value)
    {
        if (null === $this->modulesContextData) {
            $this->modulesContextData = [];
        }
        $this->modulesContextData[$key] = $value;
        return $this;
    }

    public function setModulesContextData(array $context)
    {
        $this->modulesContextData = $context;
        return $this;
    }

    public function getModulesContextData()
    {
        if (null === $this->modulesContextData) {
            $this->modulesContextData = [];
        }
        return $this->modulesContextData;
    }


    public function getNativeContextData()
    {
        if (null === $this->_nativeContextData) {
            $shopId = $this->shopId;
            $langId = $this->shopId;
            $productCardId = $this->productCardId;
            $productId = $this->productId;
            $productDetails = $this->productDetails;
            $date = $this->date;
            $groupIds = $this->groupIds;
            $currencyId = $this->currencyId;


            if (null === $shopId) {
                $shopId = E::getShopId();
            }
            if (null === $langId) {
                $langId = E::getLangId();
            }
            if (null === $productDetails) {
                $productDetails = [];
            }
            if (null === $date) {
                $date = date("Y-m-d");
            }
            if (null === $groupIds) {
                $groupNames = E::getUserData("userGroupNames", []);
                $groupIds = array_keys($groupNames);
            }
            if (null === $currencyId) {
                $currencyId = ApplicationRegistry::get("ekom.currency_id");
            }

            $this->_nativeContextData = [
                'shop_id' => $shopId,
                'lang_id' => $langId,
                'product_card_id' => $productCardId,
                'product_id' => $productId,
                'product_details' => $productDetails,
                'date' => $date,
                'group_ids' => $groupIds,
                'currency_id' => $currencyId,
            ];

        }
        return $this->_nativeContextData;
    }

    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
        return $this;
    }

    public function setLangId($langId)
    {
        $this->langId = $langId;
        return $this;
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

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function setGroupIds(array $groupIds)
    {
        $this->groupIds = $groupIds;
        return $this;
    }

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
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
        $nativeContextData = $this->getNativeContextData();
        if (null === $nativeContextData["product_card_id"]) {
            throw new EkomException("The cardId must be set");
        }


        //--------------------------------------------
        // PREPARING THE CACHE IDENTIFIER AND CACHE DELETE IDENTIFIERS
        //--------------------------------------------
        $modulesContextData = $this->modulesContextData;
        $deleteIdentifiers = $this->getDeleteIdentifiers();


        /**
         * If the developer didn't set prepare the modulesContext data,
         * we do it manually
         */
        if (null === $modulesContextData) {
            $cacheContext = ProductBoxEntityUtil::getCacheContext($deleteIdentifiers);
            $modulesContextData = $cacheContext['modulesContextData'];
            $deleteIdentifiers = $cacheContext['cacheDeleteIdentifiers'];
        }


        $productBoxContext = array_replace($modulesContextData, $nativeContextData);
        $hash = $this->getHashByCacheContext($productBoxContext);
        $hashString = "ekom-pbox-$hash";


        //--------------------------------------------
        // RETURN THE PRODUCT BOX MODEL
        //--------------------------------------------
        $model = A::cache()->get($hashString, function () use ($productBoxContext) {

            $productId = $productBoxContext["product_id"];
            $cardId = (int)$productBoxContext["product_card_id"];
            $shopId = $productBoxContext["shop_id"];
            $langId = $productBoxContext["lang_id"];
            $productDetails = $productBoxContext["product_details"];


            $model = [];
            $api = EkomApi::inst();


            try {
                if (false !== ($row = ProductBoxEntityUtil::getProductCardInfoByCardId($cardId, $shopId, $langId))) {
                    if ('1' === $row['active']) {


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
                            $ratingInfo = EkomApi::inst()->commentLayer()->getRatingInfo($cardId);


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
                            // DECORATING THE MODEL WITH MODULES
                            //--------------------------------------------
                            /**
                             * This is an opportunity for modules to decorate:
                             *
                             * - the price (to benefit the "price synopsis" below which includes tax computation
                             *          and discounts)
                             * - the quantity
                             * - ...other things
                             *
                             *
                             * Note: since we are inside a tabatha cache, modules should
                             * only use the product box context data, and provide their cache delete identifiers
                             * (using the Ekom_ProductBox_collectPreCacheData hook).
                             *
                             */
                            $preModel = [

                            ];
                            Hooks::call("Ekom_decorateBoxModel", $preModelà);



                            //--------------------------------------------
                            // PRICE SYNOPSIS
                            //--------------------------------------------
                            //--------------------------------------------
                            // ORIGINAL PRICE
                            //--------------------------------------------
                            $originalPrice = $p['price'];
                            if (null === $originalPrice) {
                                $originalPrice = $p['default_price'];
                            }
                            $originalPrice = E::trimPrice($originalPrice);




                            //--------------------------------------------
                            // TAXES AND BASE PRICE
                            //--------------------------------------------
                            $taxLayer = $api->taxLayer();
                            $taxes = $taxLayer->getTaxesByCardId($cardId, $shopId, $langId);

                            $config = [];
                            Hooks::call("Ekom_ProductBox_filterTaxGroup", $config, $productBoxContext, $p);
                            $taxDisabled = (array_key_exists("noTax", $config) && true === $config['noTax']) ? true : false;

                            if (true === $taxDisabled) {
                                $taxRatio = 1;
                                $basePrice = $originalPrice;
                                $basePriceWithoutTax = $basePrice;
                                $basePriceWithTax = $basePrice;

                            } else {
                                $taxInfo = TaxLayer::getTaxInfo($taxes, $originalPrice);
                                $taxRatio = $taxInfo['taxRatio'];
                                $taxDetails = $taxInfo['taxDetails'];

                                $basePriceWithoutTax = $taxInfo['priceWithoutTax'];
                                $basePriceWithTax = $taxInfo['priceWithTax'];
                                $basePrice = $basePriceWithTax;
                            }

                            $basePriceWithoutTax = E::trimPrice($basePriceWithoutTax);
                            $basePriceWithTax = E::trimPrice($basePriceWithTax);
                            $basePrice = E::trimPrice($basePrice);






                            //--------------------------------------------
                            // DISCOUNTS AND SALE PRICE
                            //--------------------------------------------
                            az("todo discounts");


                            $boxConf = [
                                "_price" => $originalPrice, // the original price (price from the shop_has_product table, or, if null, from product table)
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


                                "priceWithTax" => $priceWithTax,
                                "priceWithoutTax" => $priceWithoutTax,
                                "rawPriceWithTax" => $_priceWithTax,
                                "rawPriceWithoutTax" => $_priceWithoutTax,


                                "attributesString" => $attrString,
                                "attributesSelection" => $attrSelection,
                                "attributes" => $attr,
                                // rating
                                "rating_amount" => $ratingInfo['average'], // percent
                                "rating_nbVotes" => $ratingInfo['count'],

                                // tax ratio
                                "taxApplies" => true, // you could set this to false with modules
                                "taxRatio" => $taxRatio,
                                "taxDetails" => $taxDetails,
                                "taxGroupName" => $taxInfo['taxGroupName'],
                                "taxGroupLabel" => $taxInfo['taxGroupLabel'],
                                "taxAmountUnit" => $taxInfo['taxAmountUnit'],
                                "codes" => $codes,


                                // card combination
                                //--------------------------------------------
                                // PRIVATE, are removed before the result is returned
                                //--------------------------------------------
//                                "_taxes" => $taxes,
                            ];

                            $model = $boxConf;
                            /**
                             * The product details passed here are just the raw product details params in the uri
                             */
                            $model['_productDetails'] = $productDetails;

                            /**
                             * You can only subscribe to this hook if you use the tabatha cache ids
                             */
                            Hooks::call("Ekom_decorateBoxModelCachable", $model);


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


            } catch (\Exception $e) {
                $model['errorCode'] = "exception";
                $model['errorTitle'] = "Exception occurred";
                $model['errorMessage'] = $e->getMessage() . "($cardId, $productId)";
                $model['errorTrace'] = $e->getTraceAsString();

            }

            return $model;


        }, $deleteIdentifiers);


//        az(__FILE__, $model);
        if (array_key_exists('product_id', $model)) { // if model is not in error form


            //--------------------------------------------
            // DYNAMIC PART: (could not be part of the cache, unless you know exactly what you are doing)
            //--------------------------------------------
            /**
             * Adding productDetails
             * updating price if necessary
             * Note: to update price, you can use tools such as:
             *
             * - Ekom/Api/Util/ProductUtil::updateProductPrice
             *
             */
            Hooks::call("Ekom_decorateBoxModel", $model);
            unset($model['_productDetails']);


            $_priceWithTax = $model['rawPriceWithTax'];
            $_priceWithoutTax = $model['rawPriceWithoutTax'];


            //--------------------------------------------
            // NOW APPLYING DISCOUNT DYNAMICALLY (so that it's always synced with app rules)
            //--------------------------------------------
            /**
             * Actually,
             * @todo-ling: we can cache it for one day using:
             *
             * - the user group Ids
             * - the currency
             * - today's date
             *
             * However, if the discount date ends in the middle of the day,
             * we need another helper external system to clean the cache in time.
             *
             * - suggestion: try to see how well/fast it works without cache first
             *
             *
             */
            $badges = [];
            list($_salePriceWithoutTax, $_salePriceWithTax) = $api->discountLayer()->applyDiscountsByProductId($model['product_id'], $_priceWithoutTax, $_priceWithTax, $badges, $shopId, $langId);

            $salePriceWithTax = E::price($_salePriceWithTax);
            $salePriceWithoutTax = E::price($_salePriceWithoutTax);
            $model['rawSalePriceWithTax'] = $_salePriceWithTax;
            $model['rawSalePriceWithoutTax'] = $_salePriceWithoutTax;
            $model['salePriceWithTax'] = $salePriceWithTax;
            $model['salePriceWithoutTax'] = $salePriceWithoutTax;


            $model['badgeDetails'] = $badges;
            $model['hasDiscount'] = (count($badges) > 0);

            if (true === $isB2b) {
                $diff = $_priceWithoutTax - $_salePriceWithoutTax;
                if (0.0 !== (float)$_priceWithoutTax) {
                    $diffPercent = $diff / $_priceWithoutTax * 100;
                } else {
                    $diffPercent = 0;
                }
            } else {
                $diff = $_priceWithTax - $_salePriceWithTax;
                if (0.0 !== (float)$_priceWithTax) {
                    $diffPercent = $diff / $_priceWithTax * 100;
                } else {
                    $diffPercent = 0;
                }
            }
            $model['savingPercent'] = E::trimPercent($diffPercent);
            $model['savingAmount'] = E::price($diff);
            //--------------------------------------------
            //
            //--------------------------------------------
            /**
             * We need this in some cases where a renderer only renders one item.
             * This can not be cached.
             */
            $model['isB2B'] = $isB2b;


            //--------------------------------------------
            // NOW SETTING DEFAULT PRICES
            //--------------------------------------------
            if (true === $isB2b) {
                $model['price'] = $model['priceWithoutTax'];
                $model['rawPrice'] = $model['rawPriceWithoutTax'];
                $model['salePrice'] = $model['salePriceWithoutTax'];
                $model['rawSalePrice'] = $model['rawSalePriceWithoutTax'];
            } else {
                $model['price'] = $model['priceWithTax'];
                $model['rawPrice'] = $model['rawPriceWithTax'];
                $model['salePrice'] = $model['salePriceWithTax'];
                $model['rawSalePrice'] = $model['rawSalePriceWithTax'];
            }
        }

        ksort($model);
        return $model;

    }





    //--------------------------------------------
    //
    //--------------------------------------------
    private function getHashByCacheContext(array $context)
    {
        return HashTool::getHashByArray($context);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
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


    private function getDeleteIdentifiers()
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








































