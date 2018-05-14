<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Bat\UriTool;
use Core\Services\A;
use Module\Ekom\Api\Layer\CategoryLayer;
use Module\Ekom\Api\Layer\CrossSellingLayer;
use Module\Ekom\Api\Layer\ProductLayer;
use Module\EkomUserTracker\Api\Layer\EkomProductTrackerLayer;
use SokoForm\Control\SokoChoiceControl;
use SokoForm\Form\SokoForm;

class ProductDetailsControllerModuleOld
{

    public static function getModuleHandler()
    {

        $productId = $_GET['product_id'] ?? null;

        return function ($dateStart, $dateEnd) use ($productId) {

            //--------------------------------------------
            // PRODUCT DETAIL
            //--------------------------------------------
            if ($productId) {

                $productLabel = ProductLayer::getLabelByProductId($productId);

                $generalInfo = [];
                $graph1 = EkomProductTrackerLayer::getProductsRentabilityGraphByProductId($productId, $dateStart, $dateEnd, $generalInfo);
                $myGeneralInfo = [
                    "Nombre total de produits vendus" => $generalInfo['nbSales'],
                    "Nombre total de visites" => $generalInfo['nbVisits'],
                    "Montant total vendu (prix payé par le client)" => $generalInfo['salesAmount'] . " €",
                    "Taux de transformation" => $generalInfo['conversionRate'] . "%",
                ];
                $serie1 = [];
                $serie2 = [];
                foreach ($graph1 as $date => $item) {
                    $serie1[] = [$date, $item['visits']];
                    $serie2[] = [$date, $item['purchases']];
                }


                $chart1 = [
                    "title" => $productLabel,
                    "series" => [
                        "Popularité" => $serie1,
                        "Nombre de produits vendus" => $serie2,
                    ],
                ];


                //--------------------------------------------
                // SALES
                //--------------------------------------------
                $moduleName = "Ekom";
                $viewId = "back/stats/product_details_item";
                $context = [
                    "product_id" => $productId,
                    "date_start" => $dateStart,
                    "date_end" => $dateEnd,
                ];
                $listSales = A::getMorphicListConfig($moduleName, $viewId, $context);


                //--------------------------------------------
                // CROSS SALES
                //--------------------------------------------
                $crossSellsIds = CrossSellingLayer::getCrossSellProductIdsByProductId($productId);
                if ($crossSellsIds) {

                    $moduleName = "Ekom";
                    $viewId = "back/stats/product_details_item_cross_sales";
                    $context = [
                        "crossSellsIds" => $crossSellsIds,
                        "date_start" => $dateStart,
                        "date_end" => $dateEnd,
                    ];
                    $listCrossSales = A::getMorphicListConfig($moduleName, $viewId, $context);
                } else {
                    $listCrossSales = [];
                }


                $template = "Ekom/All/Stats/OrdersAndGeneralStats/product_details_item";
                $conf = [
                    "chart1" => $chart1,
                    "generalInfo" => $myGeneralInfo,
                    "alert" => [
                        "title" => "",
                        "text" => <<<EEE
Le taux de transformation est: (nombre de produits vendus / nombre de visites sur ce produit x 100)   
EEE
                        ,
                        "icon" => "fa fa-exclamation-circle",
                        "type" => "warning",
                    ],
                    "listSales" => $listSales,
                    "listCrossSales" => $listCrossSales,
                ];
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
                $context = [
                    /**
                     * Note: it seems that passing all cardIds in the uri fails for some reason.
                     * I suspect that there is a data overflow (too much data sent).
                     * So, I pass the category id only and let the template resolve the cards...
                     */
                    "date_start" => $dateStart,
                    "date_end" => $dateEnd,
//                    "cardIds" => $cardIds,
                    "category_id" => $categoryId,
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

        $choices = CategoryLayer::getItemsList([
            "alphaSort" => true,
        ]);
        $array1 = [
            0 => "Toutes",
        ];
        $choices = $array1 + $choices;


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