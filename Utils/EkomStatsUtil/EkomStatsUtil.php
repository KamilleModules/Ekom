<?php


namespace Module\Ekom\Utils\EkomStatsUtil;


use Bat\DateTool;
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
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;


/**
 * Various stats for a given date range...
 */
class EkomStatsUtil implements EkomStatsUtilInterface
{
    protected $dateStart;
    protected $dateEnd;
    protected $_ipByDate;
    protected $_orderByDate;


    public function __construct()
    {
        $this->dateStart = null;
        $this->dateEnd = null;
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
//            "currency" => E::currencyIsoCode(),
//            "shopId" => null,
        ], $options);


        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;

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
        return OrderLayer::getOrdersAmountAndCount($this->dateStart, $this->dateEnd, $options);
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
            $nbVisit = (int)$item['nb_unique'];
            if (0 === $nbVisit) {
                $v = 0;
            } else {
                $v = $nbOrder / $nbVisit * 100;
            }
            $ret[$date] = $v;
        }

        if (0 === count($ret)) {
            return 0;
        }
        return round(array_sum($ret) / count($ret), 2);
    }


    public function getNetProfit()
    {
        $date2Wholesale = ProductPurchaseStatLayer::getDate2WholeSalePrice($this->dateStart, $this->dateEnd);
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
        ], $this->dateStart, $this->dateEnd);
    }

    /**
     * Return the number of customers having ordered at least once in the next 24 hours after their account was created.
     */
    public function getNbNewCustomerOrderingFirstDay()
    {
        $markers = [];
        $qInner = "
    select 
    if (
      count(o.id) > 0,
      '1',
      '0'
    ) as hasOrdered
    from ek_user u  
    left join ek_order o on o.user_id=u.id and date(u.date_creation) >= date_sub(date(o.date), interval 1 day) 
    
        
        ";

        QuickPdoStmtTool::addDateRangeToQuery($qInner, $markers, $this->dateStart, $this->dateEnd, "date(u.date_creation)");
        $qInner .= "
group by u.email        
        ";


        $q = "

select count(*) as count from (
$qInner
) as tt

        
        ";
        return QuickPdo::fetch($q, $markers, \PDO::FETCH_COLUMN);
    }

    public function getNbAbandonedCarts()
    {
        return EkomCartTrackerCartLayer::getNbAbandonedCarts($this->dateStart, $this->dateEnd);
    }

    public function getNbNewCustomers()
    {
        return UserLayer::getNbNewUsers($this->dateStart, $this->dateEnd);
    }


    public function getNbNewNewsletterSubscribers()
    {
        return NewsletterLayer::getNbNewSubscribers($this->dateStart, $this->dateEnd);
    }

    public function getNbTotalNewsletterSubscribers()
    {
        return NewsletterLayer::getNbTotalSubscribers($this->dateStart, $this->dateEnd);
    }


    public function getGraph($type)
    {
        switch ($type) {
            case "revenue":
                return $this->getRevenueGraph();
                break;
            case "nbOrders":
                return $this->getNbOrderGraph();
                break;
            case "nbOrdersAndNbProductsAndNetProfit":
                return $this->getNbOrderAndNbProductAndNetProfitGraph();
                break;
            case "avgCart":
                return $this->getAverageCartGraph();
                break;
            case "visitors":
                return $this->getVisitorGraph();
                break;
            case "visitorsAndVisits":
                return $this->getVisitorGraph([
                    "includeVisits" => true,
                ]);
                break;
            case "conversionRate":
                return $this->getConversionRateGraph();
                break;
            case "netProfit":
                return $this->getNetProfitGraph();
                break;
            case "newCustomers":
                return $this->getNewCustomersGraph();
                break;
            case "newsletter":
                return $this->getNewsletterGraph();
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


    protected function getRevenueGraph()
    {
        $ret = [];
        $rows = OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd);
        foreach ($rows as $date => $item) {
            $ret[$date] = (float)$item["sum"];
        }
        return $ret;
    }

    protected function getNbOrderGraph()
    {
        $ret = [];
        $rows = OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd);
        foreach ($rows as $date => $item) {
            $ret[$date] = (int)$item["count"];
        }
        return $ret;
    }

    protected function getNewCustomersGraph()
    {
        $ret = [];
        $markers = [];

        $q = "
select 
date(date_creation) as date,
count(*) as nb
from ek_user

        ";

        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $this->dateStart, $this->dateEnd, "date_creation");


        $q .= "
        
group by date        
order by date_creation asc

";

        $rows = QuickPdo::fetchAll($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);


        // now, filling the holes
        DateTool::foreachDateRange($this->dateStart, $this->dateEnd, function ($curDate) use (&$ret, $rows) {
            if (false === array_key_exists($curDate, $rows)) {
                $ret[$curDate] = "0";
            } else {
                $ret[$curDate] = $rows[$curDate];
            }
        });

        return $ret;
    }

    protected function getNewsletterGraph()
    {
        $ret = [];
        $markers = [];

        $q = "
select 
date(subscribe_date) as date,
count(*) as nb
from ek_newsletter 
";
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $this->dateStart, $this->dateEnd, "subscribe_date");
        $q .= "
group by date(subscribe_date)        
order by subscribe_date asc

";


        $qAccount = "
select 
date(newsletter_registration_date) as date,
count(*) as nb
from ek_user
where newsletter_registration_date is not null
";
        QuickPdoStmtTool::addDateRangeToQuery($qAccount, $markers, $this->dateStart, $this->dateEnd, "newsletter_registration_date");
        $qAccount .= "
group by date(newsletter_registration_date)        
order by newsletter_registration_date asc

";


        $rows = QuickPdo::fetchAll($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        $rowsAccount = QuickPdo::fetchAll($qAccount, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);


        // now, filling the holes
        DateTool::foreachDateRange($this->dateStart, $this->dateEnd, function ($curDate) use (&$ret, $rows, $rowsAccount) {
            $nbCustomer = $rowsAccount[$curDate] ?? 0;
            $nbVisitor = $rows[$curDate] ?? 0;
            $nbTotal = $nbCustomer + $nbVisitor;
            $ret[$curDate] = [
                "nb_customer" => $nbCustomer,
                "nb_visitor" => $nbVisitor,
                "nb_total" => $nbTotal,
            ];
        });

        return $ret;
    }

    protected function getNbOrderAndNbProductAndNetProfitGraph()
    {
        return OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd);
    }

    protected function getAverageCartGraph()
    {
        $ret = [];
        $rows = OrderLayer::getOrdersAmountAndCountGraph($this->dateStart, $this->dateEnd);
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

    protected function getVisitorGraph(array $options = [])
    {
        $ret = [];
        $rows = $this->getIpByDate();

        $includeVisits = $options['includeVisits'] ?? false;


        if (false === $includeVisits) {

            $date2NbTotal = [];
            foreach ($rows as $row) {
                $date2NbTotal[$row["date"]] = $row['nb_unique']; // nb visitor unique per day
//                $date2NbTotal[$row["date"]] = $row['nb_total']; // nb visit total per day
            }

            DateTool::foreachDateRange($this->dateStart, $this->dateEnd, function ($curDate) use (&$ret, $date2NbTotal) {
                $value = 0;
                if (array_key_exists($curDate, $date2NbTotal)) {
                    $value = (int)$date2NbTotal[$curDate];
                }
                $ret[$curDate] = $value;
            });
        } else {
            $date2Data = [];
            foreach ($rows as $row) {
                $date2Data[$row["date"]] = [
                    'nb_total' => $row['nb_total'],
                    'nb_unique' => $row['nb_unique'],
                ];
            }

            DateTool::foreachDateRange($this->dateStart, $this->dateEnd, function ($curDate) use (&$ret, $date2Data) {
                if (array_key_exists($curDate, $date2Data)) {
                    $value = $date2Data[$curDate];
                } else {
                    $value = [
                        "nb_total" => 0,
                        "nb_unique" => 0,
                    ];
                }

                $ret[$curDate] = $value;
            });
        }


        return $ret;
    }


    public function getConversionRateGraph()
    {
        $ret = [];
        $date2Rate = [];
        $ipByDate = $this->getIpByDate();


        $this->getOrderByDate();
        $date2NbOrder = $this->_dateToNbOrder;

        foreach ($ipByDate as $item) {
            $date = $item['date'];
            $nbOrder = (array_key_exists($date, $date2NbOrder)) ? $date2NbOrder[$date] : 0;
            $nbVisit = $item['nb_unique'];
            $date2Rate[$date] = $nbOrder / $nbVisit * 100;
        }

        DateTool::foreachDateRange($this->dateStart, $this->dateEnd, function ($curDate) use (&$ret, $date2Rate) {
            $value = 0;
            if (array_key_exists($curDate, $date2Rate)) {
                $value = $date2Rate[$curDate];
            }
            $ret[$curDate] = $value;
        });

        return $ret;
    }


    public function getNetProfitGraph()
    {
        $ret = [];
        $date2Wholesale = ProductPurchaseStatLayer::getDate2WholeSalePrice($this->dateStart, $this->dateEnd);

        $date2NetProfit = $this->getRevenueGraph();


        foreach ($date2NetProfit as $date => $revenue) {
            $wholesale = (array_key_exists($date, $date2Wholesale)) ? $date2Wholesale[$date] : 0;
            $profit = $revenue - $wholesale;
            $ret[$date] = E::trimPrice($profit);
        }
        return $ret;
    }
}