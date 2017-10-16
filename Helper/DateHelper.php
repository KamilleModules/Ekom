<?php


namespace Module\Ekom\Helper;

class DateHelper
{

    public static function getMinMaxByDateRange($dateRange)
    {


        $p = explode('--', $dateRange, 2);


        if (2 === count($p)) {
            $dateMin = $p[0];
            $dateMax = $p[1];


            if ($dateMax < $dateMin) {
                $tmp = $dateMin;
                $dateMin = $dateMax;
                $dateMax = $tmp;
            }

            return [$dateMin, $dateMax];

        } else {
            return [
                $dateRange,
                $dateRange,
            ];
        }

    }

}