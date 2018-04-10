<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Core\Services\Hooks;
use Module\Ekom\Utils\EkomStatsUtil\EkomUserStatsUtil;
use Module\ThisApp\Utils\ThisAppUserStatsUtil;

class CustomerInfoControllerModule
{

    public static function getModuleHandler()
    {


        return function ($dateStart, $dateEnd) {

            $template = "Ekom/All/Stats/OrdersAndGeneralStats/customer_info";
            $conf = [];


            //--------------------------------------------
            // GENDER
            //--------------------------------------------
            $genderDistribution = EkomUserStatsUtil::getGenderDistribution([
                'date_range' => [$dateStart, $dateEnd],
            ]);
            if ($genderDistribution) {

                $male = $genderDistribution['m'] ?? 0;
                $female = $genderDistribution['me'] ?? 0;
                $miss = $genderDistribution['melle'] ?? 0;
                $realGenderDistribution = [
                    "Homme" => $male,
                    "Femme" => $female + $miss,
                ];
            } else {
                $realGenderDistribution = [];
            }
            $conf['genderDistribution'] = [
                "title" => "Distribution par sexe",
                "data" => $realGenderDistribution,
                "emptyDataMessage" => "Aucune information pour la période donnée",
            ];


            //--------------------------------------------
            // AGE
            //--------------------------------------------
            $ageDistribution = EkomUserStatsUtil::getAgeDistribution([
                'date_range' => [$dateStart, $dateEnd],
            ]);
            $realAgeDistribution = [];
            foreach ($ageDistribution as $item) {
                $ageGroup = $item['agegroup'];
                if (null === $ageGroup) {
                    $ageGroup = "Inconnu";
                }
                $total = $item['total'];
                $realAgeDistribution[$ageGroup] = $total;
            }
            $conf['ageDistribution'] = [
                "title" => "Distribution par âge",
                "data" => $realAgeDistribution,
                "emptyDataMessage" => "Aucune information pour la période donnée",
            ];


            //--------------------------------------------
            // DYNAMIC PIES
            //--------------------------------------------
            $pies = [];
            Hooks::call("Ekom_CustomerInfoStats_collectDynamicPies", $pies, $dateStart, $dateEnd);
            $conf['dynamicPies'] = $pies;


            return [
                $template,
                $conf,
            ];

        };
    }

}