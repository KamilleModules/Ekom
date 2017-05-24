<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductLayer
{


    /**
     * @return false|int, the id of the product card which slug was given, or false if there is no matching product card.
     */
    public function getProductCardIdBySlug($slug)
    {
        $shopId = ApplicationRegistry::get("ekom.front.shop_id");
        $langId = ApplicationRegistry::get("ekom.front.lang_id");
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
        ]);
    }


    public function getProductCardProducts($cardId, $shopId, $langId)
    {

        $cardId = (int)$cardId;
        $shopId = (int)$shopId;
        $langId = (int)$langId;

        return A::cache()->get("Module.Ekom.Api.Layer.getProductCardProducts.$shopId.$langId.$cardId", function () use ($cardId, $shopId, $langId) {

            $storeIds = EkomApi::inst()->shopHasStore()->readValues("store_id", ["where" => [
                ["shop_id", "=", $shopId],
            ]]);

            $productRows = QuickPdo::fetchAll("
select 
p.id as product_id,
p.reference,
p.weight,
p.price as default_price,
s.price,
s.active,
l.label,
l.description,
l.slug


from ek_product p
inner join ek_shop_has_product s on s.product_id=p.id 
inner join ek_shop_has_product_lang l on l.shop_id=s.shop_id and l.product_id=s.product_id

where 
l.lang_id=$langId
and s.shop_id=$shopId
and p.product_card_id=$cardId
        ");


            $productIds = [];
            foreach ($productRows as $row) {
                $productIds[] = $row['product_id'];
            }


            // get quantities
            $product2quantity = EkomApi::inst()->storeHasProduct()->readKeyValues("product_id", "quantity", [
                "where" => [
                    "store_id in (" . implode(", ", $storeIds) . ")",
                    "and product_id in (" . implode(", ", $productIds) . ")",
                ],
            ]);


            // add quantities to rows
            foreach ($productRows as $k => $row) {
                $pid = $row['product_id'];
                if (array_key_exists($pid, $product2quantity)) {
                    $productRows[$k]['quantity'] = $product2quantity[$pid];
                } else {
                    XLog::error("[Ekom module] - ProductLayer: quantity not found for product with id $pid in shop $shopId");
                    // note that when cache is on, this error won't be triggered
                    $productRows[$k]['quantity'] = "error";
                }
            }
            return $productRows;
        }, [
            "ek_product.*",
            "ek_shop_has_product.*",
            "ek_shop_has_product_lang.*",
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
        ]);
    }

    /**
     * @param $cardId
     * @return false|mixed
     */
    public function getProductBoxModelByCardId($cardId, $shopId = null, $langId = null)
    {


        $model = [];


        $cardId = (int)$cardId;
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.front.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.front.lang_id") : (int)$langId;


        if (false !== ($row = $this->getProductCardInfoByCardId($cardId, $shopId, $langId))) {

            if ('1' === $row['active']) {


                /**
                 * Take the list of attributes
                 */


                $defaultProductId = $row['product_id'];

                $productsInfo = $this->getProductCardProductsWithAttributes($cardId, $shopId, $langId);

                //--------------------------------------------
                // compute the attributes info for the model
                //--------------------------------------------
                $attr = $this->adaptProductWithAttributesToAttributesModel($productsInfo, $defaultProductId);


                $productIds = [];
                foreach ($productsInfo as $r) {
                    $productIds[] = $r['product_id'];
                }

//                a($row);
//                a($attr);
//                a($productsInfo);
//                az($cardId);

                $images = EkomApi::inst()->imageLayer()->getImages("productBox", [
                    $productIds,
                    $cardId,
                ]);


                $imageFileNames = array_keys($images);
                $defaultImage = $imageFileNames[0];
                foreach ($imageFileNames as $s) {
                    if (false !== strpos($s, '-default')) {
                        $defaultImage = $s;
                    }
                }

                $label = ("" !== $row['label'])?$row['label']:$row['default_label'];

                $boxConf = [
                    "images" => $images,
                    "defaultImage" => $defaultImage,
                    "label" => $label,
                    "ref" => "1436",
                    "description" => "Plateau de freeman en bois idéal pour travailler les muscles stabilisateurs, l'équilibre et la coordination. Ultra résistant grâce à son bois robuste, le plateau dispose d'une surface antidérapante.",
                    /**
                     * Is used by the widget to assign visual cues (for instance success color) to the stockText
                     * List of available types will be defined later.
                     */
                    "stockType" => "stockAvailable",
                    "stockText" => "En stock",
                    "price" => "12.69 €", // note that price includes currency (and relevant formatting)
                    // if type is null, the price is not discounted,
                    // otherwise, the discount_ data help displaying the right discounted price
                    "discount_type" => null,
                    "discount_amount" => "0",
                    "discount_price" => "0",
                    "attributes" => [
                        'weight' => [
                            "label" => "poids",
                            "values" => [
                                [
                                    "value" => "0.5 kg",
                                    "state" => "",
                                    "productUri" => "",
                                    "getProductInfoAjaxUri" => "",
                                    "product_id" => "",
                                ],
                                [
                                    "value" => "1 kg",
                                    "state" => "selected",
                                    "productUri" => "",
                                    "getProductInfoAjaxUri" => "",
                                    "product_id" => "",
                                ],
                                [
                                    "value" => "2 kg",
                                    "state" => "",
                                    "productUri" => "",
                                    "getProductInfoAjaxUri" => "",
                                    "product_id" => "",
                                ],
                                [
                                    "value" => "3 kg",
                                    "state" => "inactive",
                                    "productUri" => "",
                                    "getProductInfoAjaxUri" => "",
                                    "product_id" => "",
                                ],
                                [
                                    "value" => "4 kg",
                                    "state" => "outOfStock",
                                    "productUri" => "",
                                    "getProductInfoAjaxUri" => "",
                                    "product_id" => "",
                                ],
                                [
                                    "value" => "5 kg",
                                    "state" => "selected",
                                    "productUri" => "",
                                    "getProductInfoAjaxUri" => "",
                                    "product_id" => "",
                                ],
                            ],
                        ],
                    ],
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
                ];

                return $boxConf;
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


        az("bottom");
        return $boxConf;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function adaptProductWithAttributesToAttributesModel(array $items, $defaultProductId)
    {
        $ret = [];
        foreach ($items as $item) {
            $pid = $item['product_id'];
            $state = "";

            if ('0' === $item['active']) {
                $state = "inactive";
            } elseif ('0' === $item['quantity']) {
                $state = "outOfStock";
            } elseif ((int)$defaultProductId === (int)$pid) {
                $state = "selected";
            }

            $slug = (!empty($item['slug'])) ? $item['slug'] : $item['reference'];

            foreach ($item['attributes'] as $attr) {

                $name = $attr["name"];


                if (!array_key_exists($name, $ret)) {
                    $ret[$name] = [
                        "label" => $attr['name_label'],
                        "values" => [],
                    ];
                }

                $ret[$name]["values"][] = [
                    "value" => $attr['value'],
                    "state" => $state,
                    "productUri" => E::link("Ekom_product", ['slug' => $slug]),
                    "getProductInfoAjaxUri" => E::link("Ekom_ajaxApi") . "?action=getProductInfo&id=" . $pid,
                    "product_id" => $item['product_id'],
                ];
            }
        }
        return $ret;
    }


    private function getImagesByProductAndCard($productId, $productCardId)
    {

    }
}