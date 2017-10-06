<?php


namespace Module\Ekom\Api\Link\Product;


use Bat\CaseTool;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Link\Features\FeaturesLink;
use Module\Ekom\Utils\Api\EkomApiLink;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use SaveOrmObject\Object\Ek\CategoryHasProductCardObject;
use SaveOrmObject\Object\Ek\CategoryObject;
use SaveOrmObject\Object\Ek\FeatureLangObject;
use SaveOrmObject\Object\Ek\FeatureObject;
use SaveOrmObject\Object\Ek\FeatureValueLangObject;
use SaveOrmObject\Object\Ek\LangObject;
use SaveOrmObject\Object\Ek\ProductAttributeLangObject;
use SaveOrmObject\Object\Ek\ProductAttributeObject;
use SaveOrmObject\Object\Ek\ProductAttributeValueLangObject;
use SaveOrmObject\Object\Ek\ProductAttributeValueObject;
use SaveOrmObject\Object\Ek\ProductCardLangObject;
use SaveOrmObject\Object\Ek\ProductCardObject;
use SaveOrmObject\Object\Ek\ProductHasProductAttributeObject;
use SaveOrmObject\Object\Ek\ProductLangObject;
use SaveOrmObject\Object\Ek\ProductObject;
use SaveOrmObject\Object\Ek\ProductTypeObject;
use SaveOrmObject\Object\Ek\SellerObject;
use SaveOrmObject\Object\Ek\ShopHasProductCardLangObject;
use SaveOrmObject\Object\Ek\ShopHasProductCardObject;
use SaveOrmObject\Object\Ek\ShopHasProductLangObject;
use SaveOrmObject\Object\Ek\ShopHasProductObject;

class ProductLink extends EkomApiLink
{


    /**
     * @param array $data
     *
     * - shop_id
     * - product_card_id
     * - lang
     * //
     * - ?label
     * - slug
     * - ?description
     * - ?meta_title
     * - ?meta_description
     * - ?meta_keywords
     * - product_id
     * - ?active
     *
     * @return array
     */
    public function saveShopProductCard(array $data, array &$results = [])
    {
        $mandatories = [
            'shop_id',
            'product_card_id',
            'lang',
            'slug',
            'product_id',
        ];
        $this->handleMissing($mandatories, $data);


        $shop_id = $data['shop_id'];
        $product_card_id = $data['product_card_id'];
        $lang = $data['lang'];
        $slug = $data['slug'];
        $product_id = $data['product_id'];

        $label = $this->get('label', $data);
        $description = $this->get('description', $data);
        $metaTitle = $this->get('meta_title', $data);
        $metaDescription = $this->get('meta_description', $data);
        $metaKeywords = $this->get('meta_keywords', $data);
        $active = $this->get('active', $data);


        $lang = $this->getLangObject($lang);

        $r = [];

        ShopHasProductCardObject::createByShopIdProductCardId($shop_id, $product_card_id)
            ->setProductId($product_id)
            ->setActive($active)
            ->createShopHasProductCardLang(ShopHasProductCardLangObject::createByShopIdProductCardIdLangId($shop_id, $product_card_id, $lang->getId())
                ->setLabel($label)
                ->setSlug($slug)
                ->setDescription($description)
                ->setMetaTitle($metaTitle)
                ->setMetaDescription($metaDescription)
                ->setMetaKeywords($metaKeywords)
            )
            ->save($r);
        $results = array_merge($results, $r);

        return $results;
    }


    /**
     * @param array $data
     *
     * - lang
     * - label
     * - ?description
     * - slug
     * - ?meta_title
     * - ?meta_description
     * - ?meta_keywords
     *
     * //
     * - shop_id: required only if categories is not empty
     * - ?categories: array of category names
     *
     * @return array
     */
    public function savePhysicalProductCard(array $data, array &$results = [])
    {
        $mandatories = [
            'lang',
            'label',
            'slug',
        ];


        $categories = $this->get('categories', $data, []);
        if ($categories) {
            $mandatories[] = 'shop_id';
        }


        $this->handleMissing($mandatories, $data);


        $lang = $data['lang'];
        $label = $data['label'];
        $slug = $data['slug'];

        $description = $this->get('description', $data);
        $metaTitle = $this->get('meta_title', $data);
        $metaDescription = $this->get('meta_description', $data);
        $metaKeywords = $this->get('meta_keywords', $data);
        $shop_id = $this->get('shop_id', $data, null);

        $lang = $this->getLangObject($lang);


        $pcl = ProductCardLangObject::createBySlugLangId($slug, $lang->getId());
        $idProductCard = $pcl->getProductCardId();
        if (null === $idProductCard) {
            $idProductCard = ProductCardObject::create()->save();
        }
        $results['product_card'] = $idProductCard;
        $r = [];
        $pcl
            ->setProductCardId($idProductCard)
            ->setLang($lang)
            ->setLabel($label)
            ->setDescription($description)
            ->setSlug($slug)
            ->setMetaTitle($metaTitle)
            ->setMetaDescription($metaDescription)
            ->setMetaKeywords($metaKeywords)
            ->save($r);
        $results = array_merge($results, $r);


        if ($categories) {
            $rCats = [];
            $rChc = [];
            // first remove all categories links
            QuickPdo::delete("ek_category_has_product_card", [
                ['product_card_id', '=', $idProductCard],
            ]);


            // then create & bind all categories
            foreach ($categories as $category) {
                $idCategory = CategoryObject::createByNameShopId($category, $shop_id)
                    ->save();
                $rCats[] = $idCategory;

                $r = [];
                CategoryHasProductCardObject::create()
                    ->setCategoryId($idCategory)
                    ->setProductCardId($idProductCard)
                    ->save($r);
                $rChc[] = $r;
            }
            $results['ek_category'] = $rCats;
            $results['ek_category_has_product_card'] = $rChc;
        }


        return $results;
    }


    /**
     * @param array $data
     *
     * - shop_id
     * - product_id
     * //
     * - ?price: int|null
     * - ?wholesale_price: int=0
     * - quantity
     * - ?active: 0
     * - ?_sale_price_without_tax: decimal=0.0
     * - ?_sale_price_with_tax: decimal=0.0
     * - seller: str, name of the seller
     * - product_type: str, name of the product type
     * //
     * - lang
     * - label
     * - ?description
     * - ?slug
     * - ?out_of_stock_text
     * - ?meta_title
     * - ?meta_description
     * - ?meta_keywords
     *
     * @return array
     */
    public function saveShopProduct(array $data, array &$results = [])
    {
        $mandatories = [
            'shop_id',
            'product_id',
            //
            'quantity',
            'seller',
            'product_type',
            'lang',
            'label',
        ];
        $this->handleMissing($mandatories, $data);


        $shop_id = $data['shop_id'];
        $product_id = $data['product_id'];
        //
        $quantity = $data['quantity'];
        $seller = $data['seller'];
        $product_type = $data['product_type'];
        $lang = $data['lang'];
        $label = $data['label'];

        $price = $this->get('price', $data, null);
        $wholeSalePrice = $this->get('wholesale_price', $data, 0);
        $active = $this->get('active', $data, 0);
        $salePriceWithoutTax = $this->get('_sale_price_without_tax', $data);
        $salePriceWithTax = $this->get('_sale_price_with_tax', $data);
        $description = $this->get('description', $data);
        $slug = $this->get('slug', $data);
        $outOfStockText = $this->get('out_of_stock_text', $data);
        $metaTitle = $this->get('meta_title', $data);
        $metaDescription = $this->get('meta_description', $data);
        $metaKeywords = $this->get('meta_keywords', $data);

        $lang = $this->getLangObject($lang);


        $r = [];
        ShopHasProductObject::createByShopIdProductId($shop_id, $product_id)
            ->setPrice($price)
            ->setWholesalePrice($wholeSalePrice)
            ->setQuantity($quantity)
            ->setActive($active)
            ->setSalePriceWithoutTax($salePriceWithoutTax)
            ->setSalePriceWithTax($salePriceWithTax)
            ->setSeller(SellerObject::createByNameShopId($seller, $shop_id))
            ->setProductType(ProductTypeObject::createByNameShopId($product_type, $shop_id))
            ->createShopHasProductLang(ShopHasProductLangObject::createUpdate()
                ->setLang($lang)
                ->setLabel($label)
                ->setDescription($description)
                ->setSlug($slug)
                ->setOutOfStockText($outOfStockText)
                ->setMetaTitle($metaTitle)
                ->setMetaDescription($metaDescription)
                ->setMetaKeywords($metaKeywords)
            )
            ->save($r);

        $results = $r;
        return $results;
    }


    /**
     *
     * @param array $data
     *
     *
     * //----- physical product
     *
     * - ?cardId: if not set, one will be created
     * - ?shopId:
     * - reference
     * - weight
     * - price
     *
     * //----- physical product description
     * - lang: 3 letter iso code
     * - label
     * - ?description
     * - ?meta_title
     * - ?meta_description
     * - ?meta_keywords
     *
     * //
     * - ?attributes:
     *      - :
     *          - nameLabel
     *          - valueLabel
     *          - ?name (default=based on label version)
     *          - ?value (default=based on label version)
     *
     * - ?features:
     *      - $featureName => $featureValue| [$featureValue, $position],
     *
     * @return int, the id of the created product
     */
    public function savePhysicalProduct(array $data, array &$results = [])
    {

        // uncomment and copy paste to speed up your development
//        $dataExample = [
//            'cardId' => null,
//            'reference' => '',
//            'weight' => 0,
//            'price' => 0,
//            // physical product description
//            'lang' => 'fra',
//            'label' => '',
//            'description' => null,
//            'meta_title' => null,
//            'meta_description' => null,
//            'meta_keywords' => null,
//            'attributes' => [
//                [
//                    'nameLabel' => '',
//                    'valueLabel' => '',
//                    'name' => null,
//                    'value' => null,
//                ],
//            ],
//        ];


        $mandatories = [
            'lang',
            'reference',
            'weight',
            'price',
        ];
        $this->handleMissing($mandatories, $data);

        $lang = $data['lang'];
        $reference = $data['reference'];
        $weight = $data['weight'];
        $price = $data['price'];
        $label = $this->get('label', $data);
        $description = $this->get('description', $data);
        $metaTitle = $this->get('meta_title', $data);
        $metaDescription = $this->get('meta_description', $data);
        $metaKeywords = $this->get('meta_keywords', $data);
        $attributes = $this->get('attributes', $data, []);
        $features = $this->get('features', $data, []);
        $cardId = $this->get('cardId', $data, null);
        $shopId = $this->get('shopId', $data, E::getShopId());


        $product = ProductObject::createByReference($reference);


        $lang = $this->getLangObject($lang);


        if (null === $cardId) {
            $cardId = $product->getProductCardId();
            if (null === $cardId) {
                $cardId = ProductCardObject::create()->save();
            }
        }
        $results['ek_product_card'] = $cardId;

        $product
            ->setProductCardId($cardId)
            ->setWeight($weight)
            ->setPrice($price)
            ->createProductLang(ProductLangObject::createUpdate()
                ->setLang($lang)
                ->setLabel($label)
                ->setDescription($description)
                ->setMetaTitle($metaTitle)
                ->setMetaDescription($metaDescription)
                ->setMetaKeywords($metaKeywords)
            );
        $r = [];
        $idProduct = $product->save($r);
        $results = array_merge($results, $r);


        if ($attributes) {


            foreach ($attributes as $attribute) {
                list($nameLabel, $valueLabel) = $attribute;

                if (array_key_exists(2, $attribute)) {
                    $name = $attribute[2];
                } else {
                    $name = $this->toIdentifier($nameLabel);
                }

                if (array_key_exists(3, $attribute)) {
                    $value = $attribute[3];
                } else {
                    $value = $this->toIdentifier($valueLabel);
                }


                $r = [];
                ProductHasProductAttributeObject::createUpdate()
                    ->setProductId($idProduct)
                    ->setProductAttribute(ProductAttributeObject::createByName($name)
                        ->createProductAttributeLang(ProductAttributeLangObject::createUpdate()
                            ->setLang($lang)
                            ->setName($nameLabel)
                        )
                    )
                    ->setProductAttributeValue(ProductAttributeValueObject::createByValue($value)
                        ->createProductAttributeValueLang(ProductAttributeValueLangObject::createUpdate()
                            ->setLang($lang)
                            ->setValue($valueLabel)
                        )
                    )
                    ->save($r);
                $results['attributes'][] = $r;
            }
        }


        if ($features) {
            $featuresLink = FeaturesLink::create();
            $options = [
                "shop_id" => $shopId,
                "lang_id" => $lang->getId(),
                "technical_description" => '',
            ];
            $results['features'] = $featuresLink->saveFeatures($idProduct, $features, $options);
        }


        return $idProduct;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function toIdentifier($word)
    {
        return CaseTool::toDog($word);
    }

    private function getLangObject($lang)
    {
        if ($lang instanceof LangObject) {
            return $lang;
        }
        return LangObject::createByIsoCode($lang);
    }
}