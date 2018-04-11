<?php


namespace Module\Ekom\Helper\Stats\Modules;


use Core\Services\A;

class BestCategoriesControllerModule
{

    public static function getModuleHandler()
    {


        return function ($dateStart, $dateEnd) {

            $template = "Ekom/All/Stats/OrdersAndGeneralStats/best_categories";
            $conf = [];

            //--------------------------------------------
            // BEST CATS
            //--------------------------------------------
            $moduleName = "Ekom";
            $viewId = "back/stats/best_categories";
            $context = [
                "date_start" => $dateStart,
                "date_end" => $dateEnd,
            ];
            $listCategories = A::getMorphicListConfig($moduleName, $viewId, $context);
            $conf['listCategories'] = $listCategories;


            return [
                $template,
                $conf,
            ];

        };
    }


    private function ddd()
    {
        $q = "
select 
c.category_id,
cc.label,
sum(s.quantity) as quantity_sum,
sum(s.total) as total_sum,
sum(s.quantity * s.wholesale_price) as total_wholesale_price,
sum(s.total) - sum(s.quantity * s.wholesale_price) as total_net_profit

from ek_product_purchase_stat s 
inner join ek_product_purchase_stat_category c on c.product_purchase_stat_id=s.id
inner join ek_category cc on cc.id=c.category_id

group by category_id

order by category_id asc



";

        az(QuickPdo::fetchAll($q));
    }
}