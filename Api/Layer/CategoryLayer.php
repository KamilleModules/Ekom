<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class CategoryLayer
{

    /**
     * This breadcrumbs method returns breadcrumbs based on the uri.
     *
     * In ekom, we usually provide a variable via the registry, this variable
     * allows us to know the type of ekom page we are displaying.
     *
     * For instance, on a product card page, we can use the ekom.cardId registry variable.
     *
     */
    public function getBreadCrumbs()
    {

        /**
         * Where are we?
         * Are we on:
         *
         * - a product card page?
         */
        //--------------------------------------------
        // PRODUCT CARD
        //--------------------------------------------
        $cardId = ApplicationRegistry::get("ekom.cardId");
        if (null !== $cardId) {

            $tree = $this->getCategoryTreeByProductCardId($cardId);
            $tree = array_reverse($tree);

            // convert tree to breadcrumb "model"
            $bc = [];
            foreach ($tree as $item) {
                $label = $item['label'];
                $bc[] = [
                    "link" => E::link("Ekom_category", ['slug' => $item['slug']]),
                    "title" => "Go to " . $label,
                    "label" => $label,
                ];
            }
            return $bc;
        } else {

            return [
                [
                    "link" => "#",
                    "title" => "Go to home",
                    "label" => "Home",
                ],
                [
                    "link" => "#",
                    "title" => "product not found",
                    "label" => "Product not found",
                ],
            ];
        }
    }


    /**
     * This method return the id of the product's card categories and parent categories.
     */
    public function getCategoryIdTreeByProductId($productId, $shopId = null, $langId = null)
    {
        $api = EkomApi::inst();
        $api->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;


        return A::cache()->get("Module.Ekom.Api.Layer.CategoryLayer.getCategoryIdTreeByProductId.$shopId.$langId.$productId", function () use ($api, $shopId, $langId, $productId) {

            $ret = [];
            $cardId = EkomApi::inst()->product()->readColumn("product_card_id", [
                ["id", "=", $productId],
            ]);
            $rows = $this->getCategoryTreeByProductCardId($cardId);
            foreach ($rows as $row) {
                $ret[] = $row['id'];
            }
            return $ret;
        }, [
            'ek_product',
            // getCategoryTreeByProductCardId
            'ek_category',
            'ek_category_lang',
        ]);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getCategoryTreeByProductCardId($cardId, $shopId = null, $langId = null) // might be promoted to public someday
    {
        $api = EkomApi::inst();
        $api->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;

        /**
         * Get the category of the card for this shop
         */
        return A::cache()->get("Module.Ekom.Api.Layer.CategoryLayer.getCategoryTreeByProductCardId.$shopId.$langId.$cardId", function () use ($api, $shopId, $langId, $cardId) {
            $categoryId = $api->categoryHasProductCard()->readColumn("category_id", [
                ["product_card_id", "=", (int)$cardId],
            ]);


            $treeRows = [];


            while (false !== ($parentRow = QuickPdo::fetch("select
c.id,
c.name,
c.category_id,
l.label,
l.slug
from ek_category c 
inner join ek_category_lang l on l.category_id=c.id
where c.id=$categoryId and c.shop_id=$shopId and l.lang_id=$langId        
        "))) {
                $categoryId = $parentRow['category_id'];
                $treeRows[] = $parentRow;
                if (null === $parentRow['category_id']) {
                    break;
                }
            }

            return $treeRows;
        }, [
            'ek_category',
            'ek_category_lang',
        ]);
    }


}