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


        $label = null;
        $items = [];

        if (null !== $cardId) {


            $box = EkomApi::inst()->productLayer()->getProductBoxModelByCardId($cardId);
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