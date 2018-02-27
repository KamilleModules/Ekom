<?php


namespace Module\Ekom\Utils\EkomStatsUtil;


use Module\Ekom\Api\Layer\OrderLayer;
use Module\Ekom\Api\Layer\ProductPurchaseStatLayer;
use Module\Ekom\Utils\E;
use Module\EkomUserTracker\Api\Layer\UserTrackerLayer;

class EkomStatsUtil implements EkomStatsUtilInterface
{
    protected $dateStart;
    protected $dateEnd;
    protected $currency;
    protected $_ipByDate;
    protected $_orderByDate;


    public function __construct()
    {
        $this->dateStart = null;
        $this->dateEnd = null;
        $this->currency = null;
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
        ], $options);


        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->currency = $options['currency'];

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

        return round(array_sum($ret) / count($ret), 2);
    }


    public function getNetProfit()
    {
        $date2Wholesale = ProductPurchaseStatLayer::getDate2WholeSalePrice();
        $wholesaleTotal = array_sum($date2Wholesale);
        $rev = $this->getRevenues();
        $orderTotal = $rev[0];

        $net = $orderTotal - $wholesaleTotal;
        return $net;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getOrderByDate()
    {
        if (null === $this->_orderByDate) {
            $this->_orderByDate = OrderLayer::getOrdersAmountAndCountByDate($this->dateStart, $this->dateEnd);
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
            $this->_ipByDate = UserTrackerLayer::getIpInfo($this->dateStart, $this->dateEnd);
        }
        return $this->_ipByDate;
    }
}