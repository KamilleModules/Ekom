<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
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
            az("rr");

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


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getCategoryTreeByProductCardId($cardId) // might be promoted to public someday
    {


        /**
         * Get the category of the card for this shop
         */

        $result = A::cache()->get("CategoryLayer.getCategoryTreeByProductCardId", function () use ($cardId) {
            $api = EkomApi::inst();
            $shopId = ApplicationRegistry::get('ekom.front.shop_id');
            $langId = ApplicationRegistry::get('ekom.front.lang_id');
            $categoryId = $api->shopHasProductCard()->readColumn("category_id", [
                ["shop_id", "=", $shopId],
                ["product_card_id", "=", (int)$cardId],
            ]);


            $treeRows = [];


            while (false !== ($parentRow = QuickPdo::fetch("select
c.id,
c.name,
c.category_id,
l.label
from ek_category c 
inner join ek_category_lang l on l.category_id=c.id
where c.id=$categoryId and l.lang_id=$langId        
        "))) {
                $categoryId = $parentRow['category_id'];
                $treeRows[] = $parentRow;
                if (null === $parentRow['category_id']) {
                    break;
                }
            }

            return $treeRows;
        }, [
            'ek_category.*',
        ]);


        az($result);


    }


}