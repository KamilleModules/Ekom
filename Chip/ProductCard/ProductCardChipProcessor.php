<?php


namespace Module\Ekom\Chip\ProductCard;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Chip\ChipProcessor;
use QuickPdo\QuickPdo;

class ProductCardChipProcessor extends ChipProcessor
{
    public static function create()
    {
        return new static();
    }

    /**
     * collect:
     *      - idCard
     *      - idCardLang
     */
    public function process(ProductCardChip $chip, array &$collect = [])
    {

        QuickPdo::transaction(function () use ($chip, $collect) {


            //--------------------------------------------
            // CHECK CONF
            //--------------------------------------------
            $shopId = $this->mandatory($chip->getShopId(), 'shopId');
            $langId = $this->mandatory($chip->getLangId(), 'langId');
            $label = $this->mandatory($chip->getLabel(), 'label');
            $slug = $this->mandatory($chip->getSlug(), 'slug');

            $description = $chip->getDescription();
            $metaTitle = $chip->getMetaTitle();
            $metaDescription = $chip->getMetaDescription();
            $metaKeywords = $chip->getMetaKeywords();
            $categoryId = $chip->getCategoryId();
            $active = (int)$chip->isActive();


            //--------------------------------------------
            // EXECUTE ROUTINE
            //--------------------------------------------
            $api = EkomApi::inst();
            $idCard = $api->productCard()->create([]);


            $idCardLang = $api->productCardLang()->create([
                'product_card_id' => $idCard,
                'lang_id' => $langId,
                'label' => $label,
                'description' => $description,
                'slug' => $slug,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
            ]);


            $api->shopHasProductCard()->create([

            ]);


            //--------------------------------------------
            // FEED THE COLLECT ARRAY
            //--------------------------------------------
            $collect['idCard'] = $idCard;
            $collect['idCardLang'] = $idCardLang;


        }, function ($e) {
            $this->exception($e);
        });
    }

}