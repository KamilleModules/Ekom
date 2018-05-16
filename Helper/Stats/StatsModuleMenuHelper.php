<?php


namespace Module\Ekom\Helper\Stats;


use Bat\ArrayTool;

class StatsModuleMenuHelper
{


    public static function insertModuleLeftItemsAfter(array &$menu, string $targetItemName, array $newItems)
    {
        $index = null;
        foreach ($menu as $k => $v) {
            if ($targetItemName === $v['name']) {
                $index = $k;
                break;
            }
        }

        if (null !== $index) {
            $zeIndex = $index + 1;

            $start = array_slice($menu, 0, $zeIndex);
            $end = array_slice($menu, $zeIndex);
            $menu = array_merge($start, $newItems, $end);
        }
        //otherwise, you don't have the expected results, your responsibility to see it...
    }


    public static function getModuleLeftMenu()
    {
        return [
            [
                "name" => "orders_and_profit",
                "label" => "Commandes et CA",
            ],
            [
                "name" => "customer_accounts",
                "label" => "Comptes clients",
            ],
            [
                "name" => "product_details",
                "label" => "Détail produit",
            ],
            [
                "name" => "customer_info",
                "label" => "Informations clients",
            ],
            [
                "name" => "best_categories",
                "label" => "Meilleures catégories",
            ],
            [
                "name" => "best_sells",
                "label" => "Meilleures ventes",
            ],
            [
                "name" => "best_customers",
                "label" => "Meilleurs clients",
            ],
            [
                "name" => "best_coupons",
                "label" => "Meilleurs coupons de réduction",
            ],
            [
                "name" => "best_providers",
                "label" => "Meilleurs fournisseurs",
            ],
            [
                "name" => "best_sellers",
                "label" => "Meilleurs vendeurs",
            ],
            [
                "name" => "keywords",
                "label" => "Mots clés",
            ],
            [
                "name" => "newsletter",
                "label" => "Newsletter",
            ],
            [
                "name" => "page_not_found",
                "label" => "Pages introuvables",
            ],
            [
                "name" => "out_of_stock",
                "label" => "Quantités indisponibles",
            ],
            [
                "name" => "searches",
                "label" => "Recherches de la boutique",
            ],
            [
                "name" => "carriers_distribution",
                "label" => "Répartition par transporteur",
            ],
            [
                "name" => "partners_websites",
                "label" => "Sites affiliés",
            ],
            [
                "name" => "catalog_stats",
                "label" => "Statistiques catalogue",
            ],
            [
                "name" => "dashboard",
                "label" => "Tableau de bord statistiques",
            ],
            [
                "name" => "visits",
                "label" => "Visites et visiteurs",
            ],
            [
                "name" => "online_visitors",
                "label" => "Visiteurs en ligne",
            ],
            [
                "name" => "catalog_estimate",
                "label" => "Évaluation du catalogue",
            ],

        ];
    }
}