<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class BreadcrumbsLayer
{

    /**
     * Singleton for overriding the breadcrumbs manually.
     */
    private static $breadCrumbs = null;

    public static function setBreadcrumbs(array $breadCrumbs)
    {
        self::$breadCrumbs = $breadCrumbs;
    }


    public static function getBreadCrumbLabelsByCategoryId(int $categoryId)
    {
        $items = [];
        $cats = EkomApi::inst()->categoryLayer()->getUpCategoryInfosById($categoryId);
        if ($cats) {
            array_shift($cats); // remove home category
            foreach ($cats as $cat) {
                $items[] = $cat['label'];
            }
        }
        return $items;
    }


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

        if (null !== self::$breadCrumbs) {
            return self::$breadCrumbs;
        }

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
                    "link" => E::link("Ekom_category", [
                        'slug' => $item['slug'],
                        'type' => $item['type'],
                    ]),
                    "title" => "Go to " . $lab,
                    "label" => $lab,
                ];
            }

        } elseif (null !== $categoryId) {


            $cats = EkomApi::inst()->categoryLayer()->getUpCategoryInfosById($categoryId);


            $baseUri = E::link("Ekom_category", [
                'slug' => '{slug}',
                'type' => '{type}',
            ]);

            if ($cats) {

                array_shift($cats); // remove home category
                $last = array_pop($cats);

                foreach ($cats as $cat) {
                    $items[] = [
                        "link" => str_replace([
                            "{slug}",
                            "{type}",
                        ], [
                            $cat['slug'],
                            $cat['type'],
                        ], $baseUri),
                        "title" => "Go to " . $cat['label'],
                        "label" => $cat['label'],
                    ];
                }
                $label = $last['label'];
            }


        } else {
            $label = ApplicationRegistry::get("ekom.breadcrumbs.label");
            $items = [
                [
                    "link" => "/",
                    "title" => "Retour à la page d'accueil",
                    "label" => "Accueil",
                ],
            ];
        }

        return [
            "label" => $label,
            "items" => $items,
        ];
    }
}