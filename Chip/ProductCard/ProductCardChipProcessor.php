<?php


namespace Module\Ekom\Chip\ProductCard;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Chip\ChipProcessor;
use QuickPdo\QuickPdo;

/**
 * Processor
 * ===============
 *
 * If the card contains an already existing product, it's skipped
 *
 */
class ProductCardChipProcessor extends ChipProcessor
{
    public static function create()
    {
        return new static();
    }

    /**
     * collect:
     *      - idCard: $idCard
     *      - slug: $slug // product card lang
     *      - products: array of $productId => empty array
     *
     */
    public function process(ProductCardChip $chip, array &$collect = [])
    {

        QuickPdo::transaction(function () use ($chip, &$collect) {


            //--------------------------------------------
            // CHECK CONF
            //--------------------------------------------
            $langId = $this->mandatory($chip->getLangId(), 'langId');
            $label = $this->mandatory($chip->getLabel(), 'label');
            $slugCardLang = $this->mandatory($chip->getSlug(), 'slug');

            $description = $chip->getDescription();
            $metaTitle = $chip->getMetaTitle();
            $metaDescription = $chip->getMetaDescription();
            $metaKeywords = $chip->getMetaKeywords();


            //--------------------------------------------
            // EXECUTE ROUTINE
            //--------------------------------------------
            $api = EkomApi::inst();
            $info = EkomApi::inst()->productCardLangLayer()->getInfoBySlug($slugCardLang);
            if (false === $info) {
                $idCard = $api->productCard()->create([]);
                $api->productCardLang()->create([
                    'product_card_id' => $idCard,
                    'lang_id' => $langId,
                    'label' => $label,
                    'description' => $description,
                    'slug' => $slugCardLang,
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                    'meta_keywords' => $metaKeywords,
                ]);
            } else {
                $idCard = $info['product_card_id'];
                $api->productCardLang()->update([
                    'label' => $label,
                    'description' => $description,
                    'slug' => $slugCardLang,
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                    'meta_keywords' => $metaKeywords,
                ], [
                    'product_card_id' => $idCard,
                    'lang_id' => $langId,
                ]);
            }


            if (null !== ($category = $chip->getCategory())) {
                $categoryId = EkomApi::inst()->categoryLayer()->getCategoryIdByName($category);
                if (false !== $categoryId) {
                    $api->categoryHasProductCard()->create([
                        'category_id' => $categoryId,
                        'product_card_id' => $idCard,
                    ], true);
                } else {
                    $this->error("caegoryId not found with category $category");
                }
            }


            $products = $chip->getProducts();
            $collectProducts = [];
            if ($products) {
                $apiProduct = $api->product();
                $layerProduct = $api->productLayer();

                foreach ($products as $product) {

                    $reference = $this->mandatory($product->getReference(), 'reference');


                    $idProduct = $layerProduct->getProductIdByRef($reference);
                    $type = $this->mandatory($product->getType(), 'type');
                    $langId = $this->mandatory($product->getLangId(), 'lang_id');
                    $shopId = $this->mandatory($product->getShopId(), 'shop_id');
                    $label = $this->mandatory($product->getLabel(), 'label');


                    $weight = $product->getWeight();
                    $price = $product->getPrice();
                    $description = $product->getDescription();
                    $metaTitle = $product->getMetaTitle();
                    $metaDescription = $product->getMetaDescription();
                    $metaKeywords = $product->getMetaKeywords();
                    $typeId = $api->productTypeLayer()->getIdByName($type, true, $shopId);

                    if (false === $idProduct) { // skip existing products
                        $idProduct = $apiProduct->create([
                            'reference' => $reference,
                            'weight' => $weight,
                            'price' => $price,
                            'product_card_id' => $idCard,
                            'product_type_id' => $typeId,
                        ]);

                    } else {
                        $apiProduct->update([
                            'reference' => $reference,
                            'weight' => $weight,
                            'price' => $price,
                            'product_card_id' => $idCard,
                            'product_type_id' => $typeId,
                        ], [
                            'id' => $idProduct,
                        ]);
                    }

                    $api->productLang()->create([
                        'product_id' => $idProduct,
                        'lang_id' => $langId,
                        'label' => $label,
                        'description' => $description,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords' => $metaKeywords,
                    ], true);


                    $shopProduct = $product->getShopProduct();
                    if (null !== $shopProduct) {

                        $seller = $this->mandatory($shopProduct->getSeller(), 'seller');


                        $label = $shopProduct->getLabel();
                        $description = $shopProduct->getDescription();
                        $slug = $shopProduct->getSlug();
                        $outOfStockText = $shopProduct->getOutOfStockText();
                        $metaTitle = $shopProduct->getMetaTitle();
                        $metaDescription = $shopProduct->getMetaDescription();
                        $metaKeywords = $shopProduct->getMetaKeywords();
                        $price = $shopProduct->getPrice();
                        $wholesalePrice = $shopProduct->getWholesalePrice();
                        $quantity = $shopProduct->getQuantity();
                        $active = $shopProduct->getActive();
                        $salePriceWithoutTax = $shopProduct->getSalePriceWithoutTax();
                        $salePriceWithTax = $shopProduct->getSalePriceWithTax();


                        $sellerId = $api->sellerLayer()->getIdByName($seller, $shopId);


                        $col = $api->shopHasProduct()->readColumn('active', [
                            ['shop_id', '=', $shopId],
                            ['product_id', '=', $idProduct],
                        ]);

                        if (false === $col) {
                            $api->shopHasProduct()->create([
                                'shop_id' => $shopId,
                                'product_id' => $idProduct,
                                'price' => $price,
                                'wholesale_price' => $wholesalePrice,
                                'quantity' => $quantity,
                                'active' => $active,
                                '_sale_price_without_tax' => $salePriceWithoutTax,
                                '_sale_price_with_tax' => $salePriceWithTax,
                                'seller_id' => $sellerId,
                            ], true);
                        } else {
                            $api->shopHasProduct()->update([
                                'price' => $price,
                                'wholesale_price' => $wholesalePrice,
                                'quantity' => $quantity,
                                'active' => $active,
                                '_sale_price_without_tax' => $salePriceWithoutTax,
                                '_sale_price_with_tax' => $salePriceWithTax,
                                'seller_id' => $sellerId,
                            ], [
                                'shop_id' => $shopId,
                                'product_id' => $idProduct,
                            ]);
                        }


                        $api->shopHasProductLang()->create([
                            'shop_id' => $shopId,
                            'product_id' => $idProduct,
                            'lang_id' => $langId,
                            //
                            'label' => $label,
                            'description' => $description,
                            'slug' => $slug,
                            'out_of_stock_text' => $outOfStockText,
                            'meta_title' => $metaTitle,
                            'meta_description' => $metaDescription,
                            'meta_keywords' => $metaKeywords,
                        ], true);


                    }


                    $collectProducts[$idProduct] = [
                    ];

                    /**
                     * @todo-ling: attributes...
                     */
                }
            }


            $shopProductCard = $chip->getShopProductCard();
            if (null !== $shopProductCard) {

                $activeRef = $this->mandatory($shopProductCard->getActiveRef(), 'activeRef');
                $shopId = $this->mandatory($shopProductCard->getShopId(), 'shopId');

                $active = $shopProductCard->getActive();
                $label = $shopProductCard->getLabel();
                $slug = $shopProductCard->getSlug();
                $description = $shopProductCard->getDescription();
                $metaTitle = $shopProductCard->getMetaTitle();
                $metaDescription = $shopProductCard->getMetaDescription();
                $metaKeywords = $shopProductCard->getMetaKeywords();

                if (false !== ($activeProductId = $api->productLayer()->getProductIdByRef($activeRef))) {

                    $api->shopHasProductCard()->create([
                        'shop_id' => $shopId,
                        'product_card_id' => $idCard,
                        'product_id' => $activeProductId,
                        'active' => $active,
                    ], true);

                    $api->shopHasProductCardLang()->create([
                        'shop_id' => $shopId,
                        'product_card_id' => $idCard,
                        'lang_id' => $langId,
                        'label' => $label,
                        'slug' => $slug,
                        'description' => $description,
                        'meta_title' => $metaTitle,
                        'meta_description' => $metaDescription,
                        'meta_keywords' => $metaKeywords,
                    ], true);

                } else {
                    $this->error("active product id not found with ref: $activeRef");
                }

            }


            //--------------------------------------------
            // FEED THE COLLECT ARRAY
            //--------------------------------------------
            $collect['idCard'] = $idCard;
            $collect['slug'] = $slugCardLang; // product card lang
            $collect['products'] = $collectProducts;


        }, function ($e) {
            $this->exception($e);
        });
    }

}