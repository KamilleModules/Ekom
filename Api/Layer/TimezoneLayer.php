<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Utils\Checkout\CurrentCheckoutData;
use Module\Ekom\Utils\DistanceEstimator\DistanceEstimatorInterface;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;


class TimezoneLayer
{


    public static function getEntries()
    {
        return QuickPdo::fetchAll("select id, name from ek_timezone", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }
}