<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Exception\EkomApiException;
use Module\Ekom\Utils\AttributeSelectorHelper;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductLayer
{


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

        return A::cache()->get("Module.Ekom.Api.Layer.ProductLayer.getProductCardInfoByCardId.$langId.$shopId.$cardId", function () use ($shopId, $langId, $cardId) {

            /**
             * First get the product card info
             */
            if (false !== ($row = QuickPdo::fetch("
select
 
sl.label,
sl.slug,
sl.description,
s.product_id,
s.active,
l.label as default_label,
l.description as default_description,
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
            "ek_shop_has_product_card_lang.*",
            "ek_shop_has_product_card.*",
            "ek_product_card_lang.*",
            "ek_product_card.delete",
            "ek_product_card.update",
            "ek_shop.*",
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
l.out_of_stock_text,
ll.label as default_label,
ll.description as default_description,
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
            "ek_product.*",
            "ek_shop_has_product.*",
            "ek_shop_has_product_lang.*",
            "ek_shop.*",
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
a.id as attribute_id,
a.name as name_label,
aa.name,
v.id as value_id,
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
                    XLog::error("[Ekom module] - ProductLayer: attributes not found for product with id $pid in shop $shopId and lang $langId");
                    $productsInfo[$k]['attributes'] = ["error"];
                }
            }
            return $productsInfo;

        }, [
            "ek_product_has_product_attribute.*",
            "ek_product_attribute_lang.*",
            "ek_product_attribute_value_lang.*",
            "ek_product.delete",
            "ek_product.update",
        ]);
    }

    /**
     * @param $productId , null|int, if null, it means that you want to display the productBox for the given cardId,
     *                              if not null, it means that you want to display the productBox for a specific product.
     * @return false|mixed
     */
    public function getProductBoxModelByCardId($cardId, $shopId = null, $langId = null, $productId = null, $includeUnformatted = false)
    {

        $cardId = (int)$cardId;
        $productId = (int)$productId;
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $iIncludeUnformatted = (int)$includeUnformatted;
        $bPriceWithTax = E::conf("displayPriceWithTax");
        $iPriceWithTax = (int)$bPriceWithTax;
        $api = EkomApi::inst();

        $model = A::cache()->get("Module.Ekom.Api.Layer.getProductBoxModelByCardId.$shopId.$langId.$cardId.$productId.$iPriceWithTax", function () use ($cardId, $shopId, $langId, $productId, $api, $bPriceWithTax) {
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


                            $stockType = "stockAvailable";
                            $stockText = "in stock";
                            if (0 === (int)$p['quantity']) {
                                $stockType = "outOfStock";
                                $stockText = $p['out_of_stock_text'];
                            }


                            $priceWithoutTax = $p['price'];
                            if (null === $priceWithoutTax) {
                                $priceWithoutTax = $p['default_price'];
                            }


                            // get taxes
                            $taxLayer = $api->taxLayer();
                            $taxes = $taxLayer->getTaxesByCardId($cardId, $shopId, $langId);
                            $taxDetails = [];
                            $priceWithTax = $taxLayer->applyTaxesToPrice($taxes, $priceWithoutTax, $taxDetails);


                            $formattedPriceWithoutTax = E::price($priceWithoutTax);
                            $formattedPriceWithTax = E::price($priceWithTax);


                            if (true === $bPriceWithTax) {
                                $displayPrice = $formattedPriceWithTax;
                            } else {
                                $displayPrice = $formattedPriceWithoutTax;
                            }


                            $boxConf = [
                                "product_id" => (int)$productId,
                                "images" => $images,
                                "defaultImage" => $defaultImage,
                                "label" => $label,
                                "ref" => $p['reference'],
                                "description" => $description,
                                /**
                                 * Is used by the widget to assign visual cues (for instance success color) to the stockText
                                 * List of available types will be defined later.
                                 */
                                "stockType" => $stockType,
                                "stockText" => $stockText,


                                "priceWithoutTax" => $formattedPriceWithoutTax,
                                "priceWithTax" => $formattedPriceWithTax,
                                "displayPrice" => $displayPrice, // the price chosen by the ekom module for display


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


                            //--------------------------------------------
                            // adding un-formatted versions for discount computation
                            //--------------------------------------------
                            $unformattedPriceWithoutTax = E::trimPrice($priceWithoutTax);
                            $unformattedPriceWithTax = E::trimPrice($priceWithTax);

                            if (true === $bPriceWithTax) {
                                $displayPriceUnformatted = $unformattedPriceWithTax;
                            } else {
                                $displayPriceUnformatted = $unformattedPriceWithoutTax;
                            }
                            $boxConf["displayPriceUnformatted"] = $displayPriceUnformatted;
                            $boxConf["priceWithoutTaxUnformatted"] = $unformattedPriceWithoutTax;
                            $boxConf["priceWithTaxUnformatted"] = $unformattedPriceWithTax;


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
                    $model['errorMessage'] = "This product card is not associated with this shop, sorry";
                }


            } catch (\Exception $e) {
                $model['errorCode'] = "exception";
                $model['errorTitle'] = "Exception occurred";
                $model['errorMessage'] = $e->getMessage();
                XLog::error("[Ekom module] - ProductLayer.Exception: $e");
            }
            return $model;


        }, [
            "ek_shop_has_product_card_lang.*",
            "ek_shop_has_product_card.*",
            "ek_product_card_lang.*",
            "ek_product_card.delete",
            "ek_product_card.update",
            "ek_shop.*",
            "ek_product_has_product_attribute.*",
            "ek_product_attribute_lang.*",
            "ek_product_attribute_value_lang.*",
            "ek_product.delete",
            "ek_product.update",
            // images
            "ekomApi.image.product",
            "ekomApi.image.productCard",
            // taxes
            "ek_tax.*",
            "ek_tax_group_has_tax.*",
            "ek_tax_group.*",
            "ek_product_card_has_tax_group.*",
            "ek_shop.delete",
            "ek_lang.delete",
            "ek_product_card.delete",
        ]);


        $discountDetails = [];

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


            $discountDetails = $discounts;


            // first apply all discounts that apply on priceWithoutTax
            $priceWithoutTax = $model['priceWithoutTaxUnformatted'];
            $priceWithTax = $model['priceWithTaxUnformatted'];

            $originalPriceWithTax = $priceWithTax;
            $originalPriceWithoutTax = $priceWithoutTax;


            $taxGroupPercentage = ($priceWithTax - $priceWithoutTax) * 100 / $priceWithoutTax;
            $taxGroupPercentageBackward = 0;

            $badgesHt = [];
            $badgesTtc = [];


            $atLeastOneDiscountApplied = false;
            $lastPriceWithTax = $priceWithTax;
            foreach ($discounts as $d) {
                $t = false;

                if ('priceWithoutTax' === $d['target']) {
                    $priceWithoutTax = $layerDiscount->applyDiscountToPrice($d, $priceWithoutTax, $t);
                    $operand = $d['procedure_operand'];
                    $badgesHt[] = [$d['procedure_type'], $operand, $d['label']];

                    $priceWithTax = $priceWithoutTax + $priceWithoutTax * $taxGroupPercentage / 100;
                    if ('amount' === $d['procedure_type']) {
                        $operand = $priceWithTax - $lastPriceWithTax;
                    }
                    $badgesTtc[] = [$d['procedure_type'], $operand, $d['label']];
                    $lastPriceWithTax = $priceWithTax;

                } else {
                    $priceWithTax = $layerDiscount->applyDiscountToPrice($d, $priceWithTax, $t);


                }


                if (true === $t) {
                    $atLeastOneDiscountApplied = true;
                }
            }


            az($discounts);


            $discount_type = null;
            $discount_saving = 0;
            $discount_saving_percent = 0;
            $atLeastOneDiscountApplied = false;
            if (array_key_exists("priceWithoutTax", $discounts)) {
                $di = $discounts['priceWithoutTax'];
                foreach ($di as $k => $d) {
                    $t = false;
                    $priceWithoutTax = $layerDiscount->applyDiscountToPrice($d, $priceWithoutTax, $t);
                    if (true === $t) {
                        $atLeastOneDiscountApplied = true;

                        if (null === $discount_type) {
                            $discount_type = $d['procedure_type'];
                        } else {
                            if ($discount_type !== $d['procedure_type']) {
                                $discount_type = "hybrid";
                            }
                        }
                    } else {
                        unset($discountDetails["priceWithoutTax"][$k]);
                    }
                }
                if (true === $atLeastOneDiscountApplied) {
                    // get taxes
                    $taxLayer = $api->taxLayer();
                    $taxes = $model['_taxes'];
                    $taxDetails = [];
                    $priceWithTax = $taxLayer->applyTaxesToPrice($taxes, $priceWithoutTax, $taxDetails);
                    $model['taxDetails'] = $taxDetails;


                    // compute discount savings
                    $diff = abs($priceWithoutTax - $originalPriceWithoutTax);
                    $discount_saving = E::price($diff);
                    $discount_saving_percent = $diff / $originalPriceWithoutTax * 100;

                }
            }


            $discount_type2 = null;
            $discount_saving2 = 0;
            $discount_saving_percent2 = 0;
            if (array_key_exists("priceWithTax", $discounts)) {
                $di = $discounts['priceWithTax'];
                foreach ($di as $k => $d) {
                    $t = false;
                    $priceWithTax = $layerDiscount->applyDiscountToPrice($d, $priceWithTax, $t);
                    if (false === $t) {
                        unset($discountDetails["priceWithTax"][$k]);
                    } else {
                        $atLeastOneDiscountApplied = true;
                        if (null === $discount_type2) {
                            $discount_type2 = $d['procedure_type'];
                        } else {
                            if ($discount_type2 !== $d['procedure_type']) {
                                $discount_type2 = "hybrid";
                            }
                        }
                    }
                }


                // compute discount savings
                $diff = abs($priceWithTax - $originalPriceWithTax);
                $discount_saving2 = E::price($diff);
                $discount_saving_percent2 = $diff / $originalPriceWithTax * 100;
            }


            $model['priceWithoutTaxDiscount'] = E::price($priceWithoutTax);
            $model['priceWithTaxDiscount'] = E::price($priceWithTax);
            $model['displayPriceDiscount'] = (true === $bPriceWithTax) ? $model['priceWithTaxDiscount'] : $model['priceWithoutTaxDiscount'];

            $model['discount_type'] = (true === $bPriceWithTax) ? $discount_type2 : $discount_type;
            $model['discount_saving'] = (true === $bPriceWithTax) ? $discount_saving2 : $discount_saving;
            $model['discount_saving_percent'] = (true === $bPriceWithTax) ? $discount_saving_percent2 : $discount_saving_percent;

            $model['discountDetails'] = $discountDetails;


            //--------------------------------------------
            //
            //--------------------------------------------
            $model['hasDiscount'] = $atLeastOneDiscountApplied;
            $model['compoundBadgePercent'] = 0;

            if (false === $includeUnformatted) {
                unset($model["displayPriceUnformatted"]);
                unset($model["priceWithoutTaxUnformatted"]);
                unset($model["priceWithTaxUnformatted"]);
            }

            // remove private
            unset($model["_taxes"]);
        }

        return $model;
    }

    public function getProductBoxModelByProductId($productId, $shopId = null, $langId = null, $includeUnformatted = false)
    {
        EkomApi::inst()->initWebContext();

        $productId = (int)$productId;
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $iIncludeUnformatted = (int)$includeUnformatted;


        return A::cache()->get("Module.Ekom.Api.Layer.getProductBoxModelByProductId.$shopId.$langId.$productId.$iIncludeUnformatted", function () use ($productId, $shopId, $langId, $includeUnformatted) {
            $productId = (int)$productId;
            try {

                $cardId = EkomApi::inst()->product()->readColumn("product_card_id", [
                    ["id", "=", $productId],
                ]);
                if (false !== $cardId) {
                    return $this->getProductBoxModelByCardId($cardId, $shopId, $langId, $productId, $includeUnformatted);
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
            "ek_shop_has_product_card_lang.*",
            "ek_shop_has_product_card.*",
            "ek_product_card_lang.*",
            "ek_product_card.*",
            "ek_shop.*",
            "ek_product_has_product_attribute.*",
            "ek_product_attribute_lang.*",
            "ek_product_attribute_value_lang.*",
            "ek_product.delete",
            "ek_product.update",
            "ekomApi.image.product",
            "ekomApi.image.productCard",

        ]);
    }
}