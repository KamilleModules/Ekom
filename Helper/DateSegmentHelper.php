<?php


namespace Module\Ekom\Helper;


class DateSegmentHelper
{

    public static function getCurrentDateSegment()
    {
        /**
         * For now in Ekom, we only use 1 segment per day
         */
        return date("Y-m-d");
    }

    /**
     * @return string, the datetime corresponding to the dateSegment
     */
    public static function resolveDateSegment(string $dateSegment)
    {
        return $dateSegment . " 00:00:00";
    }
}