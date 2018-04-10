<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Bat\UriTool;
use Core\Services\A;
use Module\Ekom\Api\Layer\CategoryLayer;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Form\SokoForm;

class ProductDetailsControllerModule
{

    public static function getModuleHandler()
    {

        $productId = $_GET['product_id'] ?? null;

        return function ($dateStart, $dateEnd) use ($productId) {


            //--------------------------------------------
            // PRODUCT DETAIL
            //--------------------------------------------
            if ($productId) {

                $template = "Ekom/All/Stats/OrdersAndGeneralStats/product_details_item";
                $conf = [];
                return [
                    $template,
                    $conf,
                ];
            }
            //--------------------------------------------
            // PRODUCT LIST
            //--------------------------------------------
            else {


                $template = "Ekom/All/Stats/OrdersAndGeneralStats/product_details";
                $conf = [];


//            $catsByName = CategoryCoreLayer::create()->getSelfAndChildren("equipement");
//            $catsById = CategoryCoreLayer::create()->getSelfAndChildrenByCategoryId(1);
//            $cardIds = CategoryLayer::getCardIdsByCategoryId(6);

                $categoryId = $_GET['category_id'] ?? null;


                $cardIds = [];
                if ($categoryId) {
                    $cardIds = CategoryLayer::getCardIdsByCategoryId($categoryId);
                }
                $context = [
                    "cardIds" => $cardIds,
                ];
                $listConfig = A::getMorphicListConfig("Ekom", "back/stats/product_details", $context);


                $conf['form'] = self::getCategoryForm($categoryId);

                $conf['list'] = $listConfig;
                $conf['title'] = "Some title";
                $conf['alert'] = [
                    "title" => "Nombre d'achats comparé au nombre de vues",
                    "text" => <<<EEE
La liste ci-dessous indique le rapport, pour chaque produit, entre le nombre d'achats de ce produit et le nombre 
de visites de ce produit, pour la période donnée. 
EEE
                    ,
                    "icon" => "fa fa-exclamation-circle",
                    "type" => "warning",
                ];

                return [
                    $template,
                    $conf,
                ];
            }
        };
    }


    private static function getCategoryForm(string $categoryId = null)
    {

        $choices = CategoryLayer::getItemsList();


        return SokoForm::create()
            ->setMethod("get")
            ->addControl(SokoChoiceControl::create()
                ->setName("category_id")
                ->setLabel("Choisissez une catégorie")
                ->setChoices($choices)
                ->setProperties([
                    "onChangeRedirectUriFmt" => UriTool::uri(null, [
                        "category_id" => "{value}",
                    ], false, true),
                ])
                ->setValue($categoryId)
            );
    }
}