<?php


namespace Module\Ekom\Utils\EkomStatsUtil;


use Module\Ekom\Api\Layer\NewsletterLayer;
use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\ProductPurchaseStatLayer;
use Module\Ekom\Api\Layer\ShopLayer;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Status\EkomOrderStatus;
use Module\Ekom\Utils\E;
use Module\EkomCartTracker\Api\Layer\EkomCartTrackerCartLayer;
use Module\EkomUserTracker\Api\Layer\UserTrackerLayer;

class EkomStatsUtil implements EkomStatsUtilInterface
{
    protected $dateStart;
    protected $dateEnd;
    protected $currency;
    protected $shopId;
    protected $_ipByDate;
    protected $_orderByDate;


    public function __construct()
    {
        $this->dateStart = null;
        $this->dateEnd = null;
        $this->currency = null;
        $this->shopId = null;
        $this->_ipByDate = null;
        $this->_orderByDate = null;
        $this->_dateToNbOrder = null;
    }


    public static function create()
    {
        return new static();
    }

    public function prepare($dateStart = null, $dateEnd = null, array $options = [])
    {
        $options = array_replace([
            "currency" => E::getCurrencyIso(),
            "shopId" => null,
        ], $options);


        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->currency = $options['currency'];
        $this->shopId = $options['shopId'];

        return $this;
    }


    /**
     *
     *
     * options:
     *      - queryWhereExtra: string=null,
     *              use this to shape your base request upon anything you like
     *              (filtering by user, shop, reference, amount, cart_quantity,...)
     *
     *
     * Example:
     *----------------
     *
     *
     * $o = EkomStatsUtil::create()
     *      ->prepare($dateStart, $dateEnd, [
     *          'currency' => "EUR",
     * ]);
     *
     * a($o->getRevenues(['queryWhereExtra' => "
     *      and payment_method_extra='4x'
     * "]));
     *
     *
     * Return array:
     *      0: revenue
     *      1: nb order
     *
     *
     * @todo-ling: we should have the revenue with/without the taxes.
     * In other words, we should have the ek_order.amount and ek_order.amount_without_tax fields...
     *
     */
    public function getRevenues(array $options = [])
    {
        $options['shopId'] = $this->shopId;
        return OrderLayer::getOrdersAmountAndCount($this->dateStart, $this->dateEnd, $this->currency, $options);
    }


    public function getNbIp($unique = false)
    {
        $n = 0;
        $ipByDate = $this->getIpByDate();

        if (false === $unique) {
            foreach ($ipByDate as $item) {
                $n += $item['nb_total'];
            }
        } else {
            foreach ($ipByDate as $item) {
                $n += $item['nb_unique'];
            }
        }

        return $n;
    }


    public function getConversionRate()
    {
        $ret = [];
        $ipByDate = $this->getIpByDate();
        $this->getOrderByDate();
        $date2NbOrder = $this->_dateToNbOrder;


        foreach ($ipByDate as $item) {
            $date = $item['date'];
            $nbOrder = (array_key_exists($date, $date2NbOrder)) ? $date2NbOrder[$date] : 0;
            $nbVisit = $item['nb_unique'];
            $ret[$date] = $nbOrder / $nbVisit * 100;
        }

        if (0 === count($ret)) {
            return 0;
        }
        return round(array_sum($ret) / count($ret), 2);
    }


    public function getNetProfit()
    {
        $date2Wholesale = ProductPurchaseStatLayer::getDate2WholeSalePrice($this->dateStart, $this->dateEnd, $this->shopId);
        $wholesaleTotal = array_sum($date2Wholesale);
        $rev = $this->getRevenues();
        $orderTotal = $rev[0];

        $net = $orderTotal - $wholesaleTotal;
        return $net;
    }


    public function getNbPreparingOrder()
    {
        return OrderLayer::getNbOrderWithStatuses([
            EkomOrderStatus::STATUS_PREPARING_ORDER,
        ], $this->dateStart, $this->dateEnd, $this->shopId);
    }

    public function getNbAbandonedCarts()
    {
        return EkomCartTrackerCartLayer::getNbAbandonedCarts($this->dateStart, $this->dateEnd, $this->shopId);
    }

    public function getNbNewCustomers()
    {
        return UserLayer::getNbNewUsers($this->dateStart, $this->dateEnd, $this->shopId);
    }


    public function getNbNewNewsletterSubscribers()
    {
        return NewsletterLayer::getNbNewSubscribers($this->dateStart, $this->dateEnd, $this->shopId);
    }

    public function getNbTotalNewsletterSubscribers()
    {
        return NewsletterLayer::getNbTotalSubscribers($this->dateStart, $this->dateEnd, $this->shopId);
    }


    public function getGraph($type)
    {
        switch ($type) {
            case "revenue":
                return $this->getRevenueGraph();
                break;
            case "nbOrder":
                return $this->getNbOrderGraph();
                break;
            case "avgCart":
                return $this->getAverageCartGraph();
                break;
            case "visitor":
                return $this->getVisitorGraph();
                break;
            case "conversionRate":
                return $this->getConversionRateGraph();
                break;
            case "netProfit":
                return $this->getNetProfitGraph();
                break;
            default:
                throw new EkomException("Unknown type: $type");
                break;
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------

    protected function getOrderByDate()
    {
        if (null === $this->_orderByDate) {
            $this->_orderByDate = OrderLayer::getOrdersAmountAndCountByDate($this->dateStart, $this->dateEnd, $this->shopId);
            $this->_dateToNbOrder = [];
            foreach ($this->_orderByDate as $item) {
                $this->_dateToNbOrder[$item['date']] = $item['count'];
            }

        }
        return $this->_orderByDate;
    }


    protected function getIpByDate()
    {
        if (null === $this->_ipByDate) {
            $host = null;
            if (null !== $this->shopId) {
                $host = ShopLayer::getHostById($this->shopId);
                if (false === $host) {
                    throw new EkomException("This shop is not available anymore: " . $this->shopId);
                }
            }
            $this->_ipByDate = UserTrackerLayer::getIpInfo($this->dateStart, $this->dateEnd, $host);
        }
        return $this->_ipByDate;
    }


    protected function getRevenueGraph()
    {
        $ret = [];
        $options['shopId'] = $this->shopId;
        $rows = OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd, $this->currency, $options);
        foreach ($rows as $date => $item) {
            $ret[$date] = (float)$item["sum"];
        }
        return $ret;
    }

    protected function getNbOrderGraph()
    {
        $ret = [];
        $options['shopId'] = $this->shopId;
        $rows = OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd, $this->currency, $options);
        foreach ($rows as $date => $item) {
            $ret[$date] = (int)$item["count"];
        }
        return $ret;
    }

    protected function getAverageCartGraph()
    {
        $ret = [];
        $options['shopId'] = $this->shopId;
        $rows = OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd, $this->currency, $options);
        foreach ($rows as $date => $item) {
            $count = (int)$item['count'];
            if (0 === $count) {
                $result = 0;
            } else {
                $result = $item['sum'] / $count;
            }

            $ret[$date] = round($result, 2);
        }
        return $ret;
    }

    protected function getVisitorGraph()
    {
        $ret = [];
        $rows = $this->getIpByDate();
        foreach ($rows as $item) {
            $ret[$item['date']] = (int)$item['nb_total'];
        }
        return $ret;
    }


    public function getConversionRateGraph()
    {
        $ret = [];
        $ipByDate = $this->getIpByDate();


        $this->getOrderByDate();
        $date2NbOrder = $this->_dateToNbOrder;

        foreach ($ipByDate as $item) {
            $date = $item['date'];
            $nbOrder = (array_key_exists($date, $date2NbOrder)) ? $date2NbOrder[$date] : 0;
            $nbVisit = $item['nb_unique'];
            $ret[$date] = $nbOrder / $nbVisit * 100;
        }

        return $ret;
    }


    public function getNetProfitGraph()
    {
        $ret = [];
        $date2Wholesale = ProductPurchaseStatLayer::getDate2WholeSalePrice($this->dateStart, $this->dateEnd, $this->shopId);

        $date2NetProfit = $this->getRevenueGraph();


        foreach ($date2NetProfit as $date => $revenue) {
            $wholesale = (array_key_exists($date, $date2Wholesale)) ? $date2Wholesale[$date] : 0;
            $profit = $revenue - $wholesale;
            $ret[$date] = E::trimPrice($profit);
        }
        return $ret;
    }
}