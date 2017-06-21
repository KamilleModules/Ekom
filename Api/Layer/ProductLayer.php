<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Price\PriceChain\EkomProductPriceChain;
use Module\Ekom\Utils\AttributeSelectorHelper;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductLayer
{


    /**
     * @return false|int, the quantity for the given product, or false if something wrong happened
     */
    public function getProductQuantity($productId)
    {
        EkomApi::inst()->initWebContext();
        $shopId = ApplicationRegistry::get("ekom.shop_id");


        return A::cache()->get("Ekom.ProductLayer.getProductQuantity.$shopId.$productId", function () use ($shopId, $productId) {
            if (false !== ($quantity = EkomApi::inst()->shopHasProduct()->readColumn("quantity", [
                    ["shop_id", '=', $shopId],
                    ["product_id", '=', $productId],
                ]))
            ) {
                return $quantity;
            }
        }, [
            "ek_shop_has_product.update.$shopId.$productId",
            "ek_shop_has_product.delete.$shopId.$productId",
        ]);
    }

    /**
     * @return false|int, the id of the product card which slug was given, or false if there is no matching product card.
     */
    public function getProductCardIdBySlug($slug)
    {
        $shopId = ApplicationRegistry::get("ekom.shop_id");
        $langId = ApplicationRegistry::get("ekom.lang_id");
        if (false !== ($productCardId = EkomApi::inst()->shopHasProductCardLang()->readColumn("product_card_id", [
                ["shop_id", '=', $shopId],
                ["lang_id", '=', $langId],
                ["slug", '=', $slug],
            ]))
        ) {
            return $productCardId;
        }
        return EkomApi::inst()->productCardLang()->readColumn("product_card_id", [
            ["slug", '=', $slug],
            ["lang_id", '=', $langId],
        ]);
    }


    public function getProductCardInfoByCardId($cardId, $shopId, $langId)
    {

        /**
         * label, description, slug, active from
         *
         * shop has product card,
         * shop has product card lang,
         * product card lang
         *
         */
        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;

        return A::cache()->get("Ekom.ProductLayer.getProductCardInfoByCardId.$langId.$shopId.$cardId", function () use ($shopId, $langId, $cardId) {

            /**
             * First get the product card info
             */
            if (false !== ($row = QuickPdo::fetch("
select
 
sl.label,
sl.slug,
sl.description,
sl.meta_title,
sl.meta_description,
sl.meta_keywords,
s.product_id,
s.active,
l.label as default_label,
l.description as default_description,
l.meta_title as default_meta_title,
l.meta_description as default_meta_description,
l.meta_keywords as default_meta_keywords,
l.slug as default_slug

from ek_shop_has_product_card_lang sl 
inner join ek_shop_has_product_card s on s.shop_id=sl.shop_id and s.product_card_id=sl.product_card_id
inner join ek_product_card_lang l on l.product_card_id=sl.product_card_id and l.lang_id=sl.lang_id

where s.shop_id=$shopId 
and s.product_card_id=$cardId and sl.lang_id=$langId 
"))
            ) {
                return $row;
            }
            return false;
        }, [
            "ek_shop_has_product_card_lang.create",
            "ek_shop_has_product_card_lang.delete.$shopId.$cardId",
            "ek_shop_has_product_card_lang.update.$shopId.$cardId",
            "ek_shop_has_product_card.create",
            "ek_shop_has_product_card.delete.$shopId.$cardId",
            "ek_shop_has_product_card.update.$shopId.$cardId",
            "ek_product_card_lang.create",
            "ek_product_card_lang.delete.$cardId.$langId",
            "ek_product_card_lang.update.$cardId.$langId",
            "ek_product_card.create",
            "ek_product_card.delete.$cardId",
            "ek_product_card.update.$cardId",
            "ek_shop.create",
            "ek_shop.delete.$shopId",
            "ek_shop.update.$shopId",
        ]);
    }


    public function getProductCardProducts($cardId, $shopId, $langId)
    {
        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;

        return A::cache()->get("Module.Ekom.Api.Layer.getProductCardProducts.$shopId.$langId.$cardId", function () use ($cardId, $shopId, $langId) {

            $api = EkomApi::inst();


            $productRows = QuickPdo::fetchAll("
select 
p.id as product_id,
p.reference,
p.weight,
p.price as default_price,
s.price,
s.quantity,
s.active,
l.label,
l.description,
l.meta_title,
l.meta_description,
l.meta_keywords,
l.out_of_stock_text,
ll.label as default_label,
ll.description as default_description,
ll.meta_title as default_meta_title,
ll.meta_description as default_meta_description,
ll.meta_keywords as default_meta_keywords,
l.slug


from ek_product p
inner join ek_product_lang ll on ll.product_id=p.id
inner join ek_shop_has_product s on s.product_id=p.id 
inner join ek_shop_has_product_lang l on l.shop_id=s.shop_id and l.product_id=s.product_id

where 
l.lang_id=$langId
and ll.lang_id=$langId
and s.shop_id=$shopId
and p.product_card_id=$cardId


        ");


            return $productRows;
        }, [
            "ek_product",
            "ek_shop_has_product.create",
            "ek_shop_has_product.delete.$shopId",
            "ek_shop_has_product.update.$shopId",
            "ek_shop_has_product_lang.create",
            "ek_shop_has_product_lang.update.$shopId",
            "ek_shop_has_product_lang.delete.$shopId",
        ]);
    }


    public function getProductCardProductsWithAttributes($cardId, $shopId, $langId)
    {
        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;


        return A::cache()->get("Module.Ekom.Api.Layer.getProductCardProductsWithAttributes.$shopId.$langId.$cardId", function () use ($shopId, $langId, $cardId) {


            $productsInfo = $this->getProductCardProducts($cardId, $shopId, $langId);

            $productIds = [];
            foreach ($productsInfo as $row) {
                $productIds[] = $row['product_id'];
            }


            $rows = QuickPdo::fetchAll("
select 
h.product_id,
a.product_attribute_id as attribute_id,
a.name as name_label,
aa.name,
v.product_attribute_value_id as value_id,
v.value

from ek_product_has_product_attribute h
inner join ek_product_attribute aa on aa.id=h.product_attribute_id
inner join ek_product_attribute_lang a on a.product_attribute_id=aa.id 
inner join ek_product_attribute_value_lang v on v.product_attribute_value_id=h.product_attribute_value_id 

where a.lang_id=$langId 
and v.lang_id=$langId
and product_id in (" . implode(', ', $productIds) . ")
         
         
");


            $productId2attr = [];
            foreach ($rows as $row) {
                $pid = $row['product_id'];
                unset($row['product_id']);
                $productId2attr[$pid][] = $row;
            }

            foreach ($productsInfo as $k => $row) {
                $pid = $row['product_id'];
                if (array_key_exists($pid, $productId2attr)) {
                    $productsInfo[$k]['attributes'] = $productId2attr[$pid];
                } else {
//                    XLog::error("[Ekom module] - ProductLayer: attributes not found for product with id $pid in shop $shopId and lang $langId");
                    $productsInfo[$k]['attributes'] = [];
                }
            }
            return $productsInfo;

        }, [
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ek_product.delete",
            "ek_product.update",
        ]);
    }

    /**
     * @param $productId , null|int, if null, it means that you want to display the productBox for the given cardId,
     *                              if not null, it means that you want to display the productBox for a specific product.
     * @return false|mixed
     */
    public function getProductBoxModelByCardId($cardId, $shopId = null, $langId = null, $productId = null)
    {

        $cardId = (int)$cardId;
        $productId = (int)$productId;
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        $isB2b = ('b2b' === EkomApi::inst()->configLayer()->getBusinessType()) ? true : false;


        $iIsB2b = (int)$isB2b;
        $api = EkomApi::inst();

        $model = A::cache()->get("Module.Ekom.Api.Layer.getProductBoxModelByCardId.$shopId.$langId.$cardId.$productId.$iIsB2b", function () use ($cardId, $shopId, $langId, $productId, $api, $isB2b) {
            $model = [];

            try {
                if (false !== ($row = $this->getProductCardInfoByCardId($cardId, $shopId, $langId))) {

                    if ('1' === $row['active']) {


                        /**
                         * Take the list of attributes
                         */


                        $productsInfo = $this->getProductCardProductsWithAttributes($cardId, $shopId, $langId);
                        if (count($productsInfo) > 0) {


                            $productId = (string)$productId;
                            if ('0' === $productId) {
                                $productId = $row['product_id'];
                                if (null === $productId) {
                                    $productId = $productsInfo[0]["product_id"];
                                }
                            }


                            //--------------------------------------------
                            // compute the attributes info for the model
                            //--------------------------------------------
                            $attr = AttributeSelectorHelper::adaptProductWithAttributesToAttributesModel($productsInfo, $productId);


                            $images = $api->imageLayer()->getImages("productBox", [
                                $productId,
                                $cardId,
                            ]);
                            $imageFileNames = array_keys($images);
                            $defaultImage = $imageFileNames[0];
                            foreach ($imageFileNames as $s) {
                                if (false !== strpos($s, '-default')) {
                                    $defaultImage = $s;
                                }
                            }



                            /**
                             * Selected product
                             */
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
                            } else {
                                $label = ("" !== $row['label']) ? $row['label'] : $row['default_label'];
                            }

                            if ('' !== $p['description']) { // ek_shop_has_product_lang.description
                                $description = $p['description'];
                            } elseif ('' !== $p['default_description']) { // ek_product_lang.description
                                $description = $p['default_description'];
                            } else {
                                // ek_shop_has_product_card_lang.description and ek_product_card_lang.description
                                $description = ("" !== $row['description']) ? $row['description'] : $row['default_description'];
                            }


                            //--------------------------------------------
                            // META
                            //--------------------------------------------
                            $metaTitle = $this->getMetaTitle($p, $row, $label);
                            $metaDescription = $this->getMetaDescription($p, $row, $label, $description);
                            $metaKeywords = $this->getMetaKeywords($p, $row, $label, $description);


                            $stockType = "stockAvailable";
                            $stockText = "in stock";
                            if (0 === (int)$p['quantity']) {
                                $stockType = "outOfStock";
                                $stockText = $p['out_of_stock_text'];
                            }


                            $_price = $p['price'];
                            if (null === $_price) {
                                $_price = $p['default_price'];
                            }

                            // initializing values for merged b2b/b2c array
                            $_priceWithTax = null;
                            $taxDetails = [];
                            $taxes = [];


                            if (true === $isB2b) {
                                // ras


                            } else { // b2c


                            }
                            // get taxes, for both modes (b2b, b2b), just in case the template need the info
                            $taxLayer = $api->taxLayer();
                            $taxes = $taxLayer->getTaxesByCardId($cardId, $shopId, $langId);


                            $_priceWithTax = $taxLayer->applyTaxesToPrice($taxes, $_price, $taxDetails);
                            $_price = E::trimPrice($_price);
                            $_priceWithTax = E::trimPrice($_priceWithTax);

                            $price = E::price($_price);
                            $priceWithTax = E::price($_priceWithTax);



                            $boxConf = [
                                "product_id" => (int)$productId,
                                "quantity" => (int)$p['quantity'],
                                "images" => $images,
                                "defaultImage" => $defaultImage,
                                "label" => $label,
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
                                "stockType" => $stockType,
                                "stockText" => $stockText,


                                "price" => $price,
                                "priceWithTax" => $priceWithTax,
//                                "priceWithoutTax" => $priceWithoutTax,

                                //
                                "rawPrice" => $_price,
                                "rawPriceWithTax" => $_priceWithTax,


                                "taxDetails" => $taxDetails, // see TaxLayer.applyTaxesToPrice for more details
                                "attributes" => $attr,
                                //--------------------------------------------
                                // EXTENSION: SPECIFIC TO SOME PLUGINS
                                // consider using namespace_varName notation
                                //--------------------------------------------
                                // rating
                                "rating_amount" => "80", // percent
                                "rating_nbVotes" => "6",
                                // video
                                "video_sources" => [
                                    "/video/Larz Rocking Leaderfit Paris 2017 Step V2.mp4" => "video/mp4",
                                ],
                                //--------------------------------------------
                                // PRIVATE, are removed before the result is returned
                                //--------------------------------------------
                                "_taxes" => $taxes,
                            ];

                            $model = $boxConf;
                        } else {
                            $model['errorCode'] = "emptyProductCard";
                            $model['errorTitle'] = "Empty product card";
                            $model['errorMessage'] = "This product card does not contain any products";
                        }
                    } else {
                        /**
                         * product card not associated with this shop/lang.
                         */
                        $model['errorCode'] = "inactive";
                        $model['errorTitle'] = "Product card not active";
                        $model['errorMessage'] = "This product card is not active for this shop, sorry";
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
                $model['errorMessage'] = $e->getMessage();
                XLog::error("[Ekom module] - ProductLayer.Exception: $e");
            }
            return $model;


        }, [
            "ek_shop_has_product_card_lang.create",
            "ek_shop_has_product_card_lang.delete.$shopId.$cardId",
            "ek_shop_has_product_card_lang.update.$shopId.$cardId",
            "ek_shop_has_product_card.create",
            "ek_shop_has_product_card.delete.$shopId.$cardId",
            "ek_shop_has_product_card.update.$shopId.$cardId",
            "ek_product_card_lang.create",
            "ek_product_card_lang.delete.$cardId.$langId",
            "ek_product_card_lang.update.$cardId.$langId",
            "ek_product_card.create",
            "ek_product_card.delete.$cardId",
            "ek_product_card.update.$cardId",
            "ek_shop.create",
            "ek_shop.delete.$shopId",
            "ek_shop.update.$shopId",
            //
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ek_product.delete",
            "ek_product.update",
            //
            // images
            "ekomApi.image.product",
            "ekomApi.image.productCard",
            // taxes
            "ek_tax",
            "ek_tax_group_has_tax",
            "ek_tax_group",
            "ek_product_card_has_tax_group.create",
            "ek_product_card_has_tax_group.update.$shopId.$cardId",
            "ek_product_card_has_tax_group.delete.$shopId.$cardId",
        ]);

        //--------------------------------------------
        // NOW APPLYING DISCOUNT DYNAMICALLY (so that it's always synced with app rules)
        //--------------------------------------------
        /**
         * Actually, todo: we can cache it for one day using:
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
        if (array_key_exists('product_id', $model)) { // if model is not in error form
            $layerDiscount = $api->discountLayer();
            $discounts = $layerDiscount->getDiscountsByProductId($model['product_id'], $shopId, $langId);



            if (true === $isB2b) {
                $_price = $model['rawPrice'];
            } else {
                $_price = $model['rawPriceWithTax'];
            }

            $_salePrice = $_price;
            $badges = [];
            $atLeastOneDiscountApplied = false;
            foreach ($discounts as $d) {
                $t = false;
                $operand = $d['procedure_operand'];
                $target = $d['target']; // implicit/ignored for now with ekom order model4

                $_salePrice = $layerDiscount->applyDiscountToPrice($d, $_salePrice, $t);
                if (false !== $t) {
                    $badges[] = [
                        "type" => $d['procedure_type'],
                        "value" => $operand,
                        "label" => $d['label'],
                    ];
                    $atLeastOneDiscountApplied = true;
                }
            }


            $model['rawSalePrice'] = $_salePrice;
            $model['salePrice'] = E::price($_salePrice);


            //--------------------------------------------
            //
            //--------------------------------------------
            $model['hasDiscount'] = $atLeastOneDiscountApplied;
            $model['badgeDetails'] = $badges;


            $diff = $_price - $_salePrice;
            $diffPercent = $diff / $_price * 100;


            $model['savingPercent'] = E::trimPercent($diffPercent);
            $model['savingAmount'] = E::price($diff);
            $model['isB2B'] = $isB2b;


            // remove private
            unset($model["_taxes"]);


        }
//        a(__FILE__);
        return $model;
    }

    public function getProductBoxModel()
    {
        $cardId = ApplicationRegistry::get("ekom.cardId");
        $ref = ApplicationRegistry::get("ekom.productRef");
        if (null === $ref) {
            return EkomApi::inst()->productLayer()->getProductBoxModelByCardId($cardId);
        } else {
            return EkomApi::inst()->productLayer()->getProductBoxModelByProductRef($ref);
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getProductBoxModelByProductRef($productRef, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();

        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;


        return A::cache()->get("Module.Ekom.Api.Layer.getProductBoxModelByProductId.$shopId.$langId.$productRef", function () use ($productRef, $shopId, $langId) {
            try {
                $row = EkomApi::inst()->product()->readOne([
                    'where' => [
                        ["reference", "=", $productRef],
                    ],
                ]);
                if (false !== $row) {
                    return $this->getProductBoxModelByCardId($row['product_card_id'], $shopId, $langId, $row['id']);
                }
                $model['errorCode'] = "SqlRequestFailed";
                $model['errorTitle'] = "sqlRequestFailed";
                $model['errorMessage'] = "the sql request failed unexpectedly, are you sure pdo is in exceptionMode?";
                XLog::error("[Ekom module] - ProductLayer.getProductBoxModelByProductId: " . $model['errorMessage']);
            } catch (\Exception $e) { // suppose pdo is in exception mode
                $model['errorCode'] = "exception";
                $model['errorTitle'] = "Exception occurred";
                $model['errorMessage'] = $e->getMessage();
                XLog::error("[Ekom module] - ProductLayer.getProductBoxModelByProductId.Exception: $e");
            }
            return $model;
        }, [
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
        ]);
    }

    public function getProductBoxModelByProductId($productId, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();

        $productId = (int)$productId;
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;


        return A::cache()->get("Module.Ekom.Api.Layer.getProductBoxModelByProductId.$shopId.$langId.$productId", function () use ($productId, $shopId, $langId) {
            $productId = (int)$productId;
            try {

                $cardId = EkomApi::inst()->product()->readColumn("product_card_id", [
                    ["id", "=", $productId],
                ]);
                if (false !== $cardId) {
                    return $this->getProductBoxModelByCardId($cardId, $shopId, $langId, $productId);
                }
                $model['errorCode'] = "SqlRequestFailed";
                $model['errorTitle'] = "sqlRequestFailed";
                $model['errorMessage'] = "the sql request failed unexpectedly, are you sure pdo is in exceptionMode?";
                XLog::error("[Ekom module] - ProductLayer.getProductBoxModelByProductId: " . $model['errorMessage']);
            } catch (\Exception $e) { // suppose pdo is in exception mode
                $model['errorCode'] = "exception";
                $model['errorTitle'] = "Exception occurred";
                $model['errorMessage'] = $e->getMessage();
                XLog::error("[Ekom module] - ProductLayer.getProductBoxModelByProductId.Exception: $e");
            }
            return $model;
        }, [
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
        ]);
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


}