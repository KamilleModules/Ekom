<?php


namespace Module\Ekom\Api\Layer;

use Bat\CaseTool;
use Core\Services\Hooks;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class ProductHelperLayer
{


    /**
     * Removes a product from the ekom system,
     * and provides opportunities for tabatha cache to kick in.
     * This means you should always use THIS method instead of removing
     * entry manually from the database.
     *
     * Affected tables are:
     *
     * - ek_product_has_discount
     * - ek_product_comment
     * - ek_shop_has_product_lang
     * - ek_shop_has_product
     * - ek_product_lang
     * - ek_product_has_product_attribute
     * - ek_product_bundle_has_product
     * - ek_user_has_product
     * - ek_product_has_feature
     * - ek_product
     *
     *
     * Then, if the removed product is the last one of its card:
     *
     * - ek_product_card_has_discount
     * - ek_product_card_lang
     * - ek_category_has_product_card
     * - ek_shop_has_product_card_lang
     * - ek_shop_has_product_card
     * - ek_product_card
     *
     *
     *
     *
     * Note: if a card only contains the erased product, then the card is erased too.
     * (not sure if that's the best behaviour, but at least it's safer from a front's perspective)
     *
     * Note2: if an attribute is only used by the removed product
     * the attribute is NOT removed, you need to use another method to remove phantom product attributes.
     *
     *
     *
     *
     *
     *
     */
    public function removeProductById($id)
    {
        $id = (int)$id;
        if (false !== ($info = EkomApi::inst()->productLayer()->getProductInfo($id))) {


            $tables = [
                "ek_product_has_discount",
                "ek_product_comment",
                "ek_shop_has_product_lang",
                "ek_shop_has_product",
                "ek_shop_has_product",
                "ek_product_lang",
                "ek_product_has_product_attribute",
                "ek_product_bundle_has_product",
                "ek_user_has_product",
                "ek_product_has_feature",
            ];


            foreach ($tables as $table) {
                QuickPdo::delete($table, [
                    ["product_id", "=", $id],
                ]);
            }
            QuickPdo::delete("ek_product", [
                ["id", "=", $id],
            ]);

            $cardId = $info['product_card_id'];
            $count = QuickPdo::count("ek_product", [
                ["product_card_id", '=', $cardId],
            ]);

            if (0 === $count) {
                $tables = [
                    "ek_product_card_has_discount",
                    "ek_product_card_lang",
                    "ek_category_has_product_card",
                    "ek_shop_has_product_card_lang",
                    "ek_shop_has_product_card",
                ];
                foreach ($tables as $table) {
                    QuickPdo::delete($table, [
                        ["product_card_id", "=", $cardId],
                    ]);
                }
                QuickPdo::delete("ek_product_card", [
                    ["id", "=", $cardId],
                ]);
            }


            $idInfo = [$id, $cardId];
            Hooks::call("Ekom_ProductHelper_removeProductById_after", $idInfo);

        }
    }

    /**
     * @param array $data :
     *
     *      - ?card_id (if not set, one will be created)
     *      - category_id
     *      - reference
     *      - weight
     *      - price
     *      - label
     *      - description
     *      - meta_title
     *      - meta_description
     *      - meta_keywords
     *      - wholesale_price
     *      - quantity
     *      - slug
     *      - product_type
     *      - attributes:
     *          - key (label) => value (label)
     *
     *
     * @return array [productId, cardId]
     */
    public function insertQuickProduct(array $data, $shopId, $langId)
    {


        $theCardId = (array_key_exists('card_id', $data)) ? (int)$data['card_id'] : null;
        $shopId = (int)$shopId;
        $langId = (int)$langId;

        //
        $apiProduct = EkomApi::inst()->product();
        $apiProductLang = EkomApi::inst()->productLang();
        $apiShopHasProduct = EkomApi::inst()->shopHasProduct();
        $apiShopHasProductLang = EkomApi::inst()->shopHasProductLang();
        $apiProductCard = EkomApi::inst()->productCard();
        $apiProductCardLang = EkomApi::inst()->productCardLang();
        $apiShopHasProductCard = EkomApi::inst()->shopHasProductCard();
        $apiShopHasProductCardLang = EkomApi::inst()->shopHasProductCardLang();
        $apiProductHasProductAttribute = EkomApi::inst()->productHasProductAttribute();
        $apiProductAttributeLang = EkomApi::inst()->productAttributeLang();
        $apiProductAttributeValueLang = EkomApi::inst()->productAttributeValueLang();
        $apiAttributeLayer = EkomApi::inst()->attributeLayer();
        $apiCatHasCard = EkomApi::inst()->categoryHasProductCard();


        $data = array_replace([
            'meta_title' => "",
            'meta_description' => "",
            'meta_keywords' => "",
            'attributes' => [],
        ], $data);


        $productTypeId = EkomApi::inst()->productLayer()->insertTypeIfNotExist($data['product_type']);


        if (null === $theCardId) {
            $cardId = $apiProductCard->create([]);
            $apiProductCardLang->create([
                "product_card_id" => $cardId,
                "lang_id" => $langId,
                "description" => "",
                "slug" => $data['slug'],
                "meta_title" => "",
                "meta_description" => "",
                "meta_keywords" => "",
            ]);

            $catId = $data['category_id'];
            $apiCatHasCard->create([
                "category_id" => $catId,
                "product_card_id" => $cardId,
            ]);

        } else {
            $cardId = $theCardId;
        }


        $productId = $apiProduct->create([
            'reference' => $data['reference'],
            'weight' => $data['weight'],
            'price' => $data['price'],
            'product_card_id' => $cardId,
            'product_type_id' => $productTypeId,
        ]);


        $apiProductLang->create([
            "product_id" => $productId,
            "lang_id" => $langId,
            "label" => $data['label'],
            "description" => $data['description'],
            "meta_title" => $data['meta_title'],
            "meta_description" => $data['meta_description'],
            "meta_keywords" => $data['meta_keywords'],
        ]);

        $apiShopHasProduct->create([
            "shop_id" => $shopId,
            "product_id" => $productId,
            "price" => "",
            "wholesale_price" => $data['wholesale_price'],
            "quantity" => $data['quantity'],
            "active" => "1",
            "_sale_price_without_tax" => "",
            "_sale_price_with_tax" => "",
        ]);


        $apiShopHasProductLang->create([
            "shop_id" => $shopId,
            "product_id" => $productId,
            "lang_id" => $langId,
            "label" => "",
            "description" => "",
            "slug" => "",
            "out_of_stock_text" => "",
            "meta_title" => "",
            "meta_description" => "",
            "meta_keywords" => "",
        ]);


        if (null === $theCardId) {

            $apiShopHasProductCard->create([
                'shop_id' => $shopId,
                'product_card_id' => $cardId,
                'product_id' => $productId,
                'active' => "1",
            ]);
            $apiShopHasProductCardLang->create([
                'shop_id' => $shopId,
                'product_card_id' => $cardId,
                'lang_id' => $langId,
                'label' => "",
                'slug' => "",
                'description' => "",
                'meta_title' => "",
                "meta_description" => "",
                "meta_keywords" => "",
            ]);
        }


        $this->refreshProductById($productId, $shopId);


        foreach ($data['attributes'] as $key => $value) {


            $_key = CaseTool::toFlea($key);
            $_value = CaseTool::toFlea($value);

            $attrId = $apiAttributeLayer->insertAttributeIfNotExist($_key);
            $attrValueId = $apiAttributeLayer->insertAttributeValueIfNotExist($_value);


            if (false === QuickPdo::fetch("select lang_id from ek_product_attribute_lang where product_attribute_id=$attrId and lang_id=$langId and name=:name", [
                    "name" => $key,
                ])
            ) {
                $apiProductAttributeLang->create([
                    "product_attribute_id" => $attrId,
                    "lang_id" => $langId,
                    "name" => $key,
                ]);
            }

            if (false === QuickPdo::fetch("select lang_id from ek_product_attribute_value_lang where product_attribute_value_id=$attrValueId and lang_id=$langId and value=:value", [
                    "value" => $value,
                ])
            ) {
                $apiProductAttributeValueLang->create([
                    "product_attribute_value_id" => $attrValueId,
                    "lang_id" => $langId,
                    "value" => $value,
                ]);
            }

            $apiProductHasProductAttribute->create([
                "product_id" => $productId,
                "product_attribute_id" => $attrId,
                "product_attribute_value_id" => $attrValueId,
            ]);
        }


        return [$productId, $cardId];
    }


    public function refreshProductById($productId, $shopId = null)
    {
        EkomApi::inst()->discountLayer()->refreshDiscountsByProductId($productId, $shopId);
    }

}


