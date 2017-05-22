<?php


namespace Module\Ekom\Api\Layer;


use Kamille\Architecture\Registry\ApplicationRegistry;

class BreadCrumbsLayer
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

        $cardId = ApplicationRegistry::get("ekom.cardId");
        if (null !== $cardId) {
            // todo
        }

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