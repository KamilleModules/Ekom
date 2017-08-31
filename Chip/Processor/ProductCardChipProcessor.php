<?php


namespace Module\Ekom\Chip\ProductCard;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Chip\Generated\ProductCardChip;
use OrmTools\Util\Chip\Processor\ChipProcessor;
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


            $api = EkomApi::inst();

            //--------------------------------------------
            // CHECK CONF
            //--------------------------------------------
            $langId = $this->mandatory($chip->getLangId(), 'langId');


            /**
             * We use the product card lang table to detect existence of product card,
             * because the product card contains only one column
             */
            // PRODUCT CARD
            //--------------------------------------------
            $slugCardLang = $this->mandatory($chip->getSlug(), 'slug');
            $info = $api->productCardLangLayer()->getInfoBySlug($slugCardLang, $langId);
            if (false !== $info) {
                $idProductCard = $info['product_card_id'];
            } else {
                $idProductCard = $api->productCard()->create([]);

            }


            // PRODUCT CARD LANG
            //--------------------------------------------
            $data = [
                'product_card_id' => $idProductCard,
                'lang_id' => $langId,
                'label' => $this->mandatory($chip->getLabel(), 'label'),
                'description' => $chip->getDescription(),
                'slug' => $slugCardLang,
                'meta_title' => $chip->getMetaTitle(),
                'meta_description' => $chip->getMetaDescription(),
                'meta_keywrods' => $chip->getMetaKeywords(),
            ];

            $idProductCardLang = $api->productCardLang()->push([
                'slug' => $slugCardLang,
                'lang_id' => $langId,
            ], $data);


            // CATEGORY
            //--------------------------------------------
            $category = $chip->getCategory();
            $categoryId = null;
            if (null !== $category) {
                $categoryId = $api->categoryLayer()->getCategoryIdByName($category);
                $api->categoryHasProductCard()->create([
                    'category_id' => $categoryId,
                    'product_card_id' => $idProductCard,
                ], true);
            }


            // PRODUCT
            //--------------------------------------------
            $products = $chip->getProducts();
            $collectProducts = [];
            if ($products) {

                foreach ($products as $product) {

                    $reference = $this->mandatory($product->getReference(), 'reference');


                    $idProduct = $api->product()->push([
                        'reference' => $reference,
                    ], [
                        'reference' => $reference,
                        'weight' => $product->getWeight(),
                        'price' => $this->mandatory($product->getPrice(), 'price'),
                        'product_card_id' => $idProductCard,
                        'product_type_id' => $api->productTypeLayer()->getIdByName($this->mandatory($product->getProductType(), 'type'), true, $shopId),
                    ]);


                    // PRODUCT LANG
                    //--------------------------------------------
                    $api->productLang()->push([
                        'product_id' => $idProduct,
                        'lang_id' => $langId,
                    ], [
                        'product_id' => $idProduct,
                        'lang_id' => $langId,
                        'label' => $this->mandatory($product->getLabel(), 'label'),
                        'description' => $product->getDescription(),
                        'meta_title' => $product->getMetaTitle(),
                        'meta_description' => $product->getMetaDescription(),
                        'meta_keywords' => $product->getMetaKeywords(),
                    ], true);


                    // SHOP HAS PRODUCT
                    //--------------------------------------------
                    $shopProduct = $product->getShopProduct();
                    if (null !== $shopProduct) {




                        $api->shopHasProduct()->push([
                            'shop_id' => $shopProduct->getShopId(),
                            'product_id' => $idProduct,
                        ], [
                            'shop_id' => $shopId,
                            'product_id' => $idProduct,
                            'price' => $shopProduct->getPrice(),
                            'wholesale_price' => $shopProduct->getWholesalePrice(),
                            'quantity' => $shopProduct->getQuantity(),
                            'active' => $shopProduct->getActive(),
                            '_sale_price_without_tax' => $shopProduct->getSalePriceWithoutTax(),
                            '_sale_price_with_tax' => $shopProduct->getSalePriceWithTax(),
                            'seller_id' => $api->sellerLayer()->getIdByName($this->mandatory($shopProduct->getSeller(), 'seller'), $shopId),
                        ]);



                        // SHOP HAS PRODUCT LANG
                        //--------------------------------------------
                        $api->shopHasProductLang()->push([
                            'shop_id' => $shopId,
                            'product_id' => $idProduct,
                            'lang_id' => $langId,
                        ], [
                            'shop_id' => $shopId,
                            'product_id' => $idProduct,
                            'lang_id' => $langId,
                            //
                            'label' => $shopProduct->getLabel(),
                            'description' => $shopProduct->getDescription(),
                            'slug' => $shopProduct->getSlug(),
                            'out_of_stock_text' => $shopProduct->getOutOfStockText(),
                            'meta_title' => $shopProduct->getMetaTitle(),
                            'meta_description' => $shopProduct->getMetaDescription(),
                            'meta_keywords' => $shopProduct->getMetaKeywords(),
                        ]);


                    }


                    $collectProducts[$idProduct] = [
                    ];


                }
            }




            $shopProductCard = $chip->getShopProductCard();
            if (null !== $shopProductCard) {


                // SHOP HAS PRODUCT CARD
                //--------------------------------------------
                $api->shopHasProductCard()->push([

                ],[
                    'shop_id' => $shopId,
                    'product_card_id' => $idProductCard,
                    'product_id' => $activeProductId,
                    'active' => $active,
                ], true);


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