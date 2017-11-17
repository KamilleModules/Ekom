<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
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
        $cardId = ApplicationRegistry::get("ekom.cardId");
        $categoryId = ApplicationRegistry::get("ekom.categoryId");
//        az($categoryId);


        $label = null;
        $items = [];

        if (null !== $cardId) {


            $box = ProductBoxLayer::getProductBoxByCardId($cardId);

            if (false === array_key_exists("errorCode", $box)) {
                $label = $box['label'];
            }


            $tree = EkomApi::inst()->categoryLayer()->getCategoryTreeByProductCardId($cardId);
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