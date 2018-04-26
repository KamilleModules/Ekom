<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class BreadcrumbsLayer
{


    /**
     *
     * Return a breadcrumbs model.
     * https://github.com/lingtalfi/Models/blob/master/Breadcrumbs/breadcrumbs.md
     *
     *
     *
     *
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
        //--------------------------------------------
        // PRODUCT CARD
        //--------------------------------------------
        $productReferenceId = ApplicationRegistry::get("ekom.productReferenceId");
        $categoryId = ApplicationRegistry::get("ekom.categoryId");


        $label = null;
        $items = [];

        if (null !== $productReferenceId) {


            $box = ProductBoxLayer::getProductBoxByProductReferenceId($productReferenceId);
            $label = $box['label'];
            $cardId = $box['product_card_id'];
            $tree = EkomApi::inst()->categoryLayer()->getCategoryTreeByProductCardId($cardId);

            /**
             * The product card probably isn't bound to any category,
             * the administrator should be aware of that.
             * In the product page, this will result in an incomplete breadcrumb.
             */
            if (false === $tree) {
                $tree = [];
                XLog::error("[Ekom.BreadcrumbsLayer] -- it seems that the product card id $cardId isn't bound to any category yet. Please fix this. The uri was: " . $_SERVER['REQUEST_URI']);
            }


            $tree = array_reverse($tree);
            foreach ($tree as $item) {
                $lab = $item['label'];
                $items[] = [
                    "link" => E::link("Ekom_category", ['slug' => $item['slug']]),
                    "title" => "Go to " . $lab,
                    "label" => $lab,
                ];
            }

        } elseif (null !== $categoryId) {


            $cats = EkomApi::inst()->categoryLayer()->getUpCategoryInfosById($categoryId);

            $baseUri = E::link("Ekom_category", [
                'slug' => '%s',
            ]);

            if ($cats) {

                $last = array_pop($cats);

                foreach ($cats as $cat) {
                    $items[] = [
                        "link" => sprintf($baseUri, $cat['slug']),
                        "title" => "Go to " . $cat['label'],
                        "label" => $cat['label'],
                    ];
                }
                $label = $last['label'];
            }


        } else {
            $label = ApplicationRegistry::get("ekom.breacrumbs.label");
            $items = [
                [
                    "link" => "#",
                    "title" => "Go to home",
                    "label" => "Home",
                ],
            ];
        }


        return [
            "label" => $label,
            "items" => $items,
        ];
    }
}