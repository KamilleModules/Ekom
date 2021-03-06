<?php


namespace Module\Ekom\Api\Layer;


use Bat\DateTool;
use Core\Services\A;
use Core\Services\Hooks;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\Status\EkomOrderStatus;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;


/**
 *
 * To change an order status
 * ==============================
 * In ekom, the only ways to update an order status should be the following methods (from this class):
 *
 * - addOrderStatusByCode
 * - addOrderStatusById
 *
 * This will ensure consistency in the application, as a lot of modules will want to hooks to those methods.
 *
 *
 */
class OrderLayer
{

    private static $orderInfoItems = [];

    public static function setCarrierTrackingNumberByOrderId(int $orderId, $carrierTrackingNumber)
    {
        if (false !== ($row = self::getOrderInfo($orderId))) {
            $orderDetails = $row['order_details'];
            $orderDetails['carrier_tracking_number'] = $carrierTrackingNumber;
            QuickPdo::update("ek_order", [
                "order_details" => serialize($orderDetails),
            ], [
                ["id", '=', $orderId],
            ]);
            return true;
        }
        return false;
    }

    public static function getOrderIdByReference(string $reference)
    {
        return QuickPdo::fetch("select id from ek_order where reference=:ref", [
            "ref" => $reference,
        ], \PDO::FETCH_COLUMN);
    }


    public static function getNbPurchasedProductsByDateRange($dateStart = null, $dateEnd = null)
    {
        $markers = [];
        $q = "
select sum(cart_quantity) as cart_quantity_total 
from ek_order 
";

        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date(date)");

        return (int)QuickPdo::fetch($q, $markers, \PDO::FETCH_COLUMN);
    }


    public static function getStoreInfoByStoreAddressId(int $storeAddressId)
    {
        return QuickPdo::fetch("
select 
s.label,
a.*,
c.iso_code as country

from ek_store s 
inner join ek_address a on a.id=s.address_id
inner join ek_country c on c.id=a.country_id

where s.address_id=$storeAddressId
        
        ");
    }


    public static function getUserIdByOrderId(int $orderId)
    {
        return QuickPdo::fetch("select user_id from ek_order where id=$orderId", [], \PDO::FETCH_COLUMN);
    }

    public static function getTrackerIdentifiersByOrderId($id)
    {
        $id = (int)$id;
        return QuickPdo::fetchAll("
select track_identifier 
from ek_invoice 
where order_id=$id
and track_identifier != ''        
        ", [], \PDO::FETCH_COLUMN);
    }


    public static function getOrdersAmountAndCountByDate($dateStart = null, $dateEnd = null)
    {
        $markers = [];
        $q = "select date(date) as date, sum(amount) as sum, count(*) as count  from ek_order where 1";

        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");
        $q .= " group by date(date)";
        $all = QuickPdo::fetchAll($q, $markers);
        return $all;
    }


    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @return array, return the total amount for the date range in the given currency iso code
     */
    public static function getOrdersAmountAndCount($dateStart = null, $dateEnd = null, array $options = [])
    {

        $options = array_replace([
            'queryWhereExtra' => '',
            'queryWhereExtraMarkers' => [],
            'useAmountWithTax' => false,
        ], $options);


        $useAmountWithTax = $options['useAmountWithTax'];


        $qExtraMarkers = $options['queryWhereExtraMarkers'];


        if (true === $useAmountWithTax) {
            $q = "select sum(amount) as sum, count(*) as count  from ek_order where 1";
        } else {
            $q = "select sum(cart_total_tax_excluded+shipping_cost_tax_excluded) as sum, count(*) as count  from ek_order where 1";
        }

        $markers = $qExtraMarkers;
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");
        $res = QuickPdo::fetch($q, $markers);

        return [
            $res['sum'],
            $res['count'],
        ];

    }


    public static function getOrdersAmountAndCountGraph($dateStart = null, $dateEnd = null, array $options = [])
    {

        $options = array_replace([
            'queryWhereExtra' => '',
            'queryWhereExtraMarkers' => [],
            'useAmountWithTax' => false,
        ], $options);

        $useAmountWithTax = $options['useAmountWithTax'];


        $qExtra = $options['queryWhereExtra'];
        $qExtraMarkers = $options['queryWhereExtraMarkers'];


        if (true === $useAmountWithTax) {
            $q = "select date(date) as date, sum(amount) as sum, count(*) as count, sum(cart_quantity) as sum_nbItems  from ek_order where 1";
        } else {
            $q = "select date(date) as date, sum(cart_total_tax_excluded + shipping_cost_tax_excluded) as sum, count(*) as count, sum(cart_quantity) as sum_nbItems  from ek_order where 1";
        }


        $markers = $qExtraMarkers;
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");
        if ($qExtra) {
            $q .= $qExtra;
        }
        $q .= " group by date(date)";


        $all = QuickPdo::fetchAll($q, $markers);

        /**
         * @deprecated
         * Flattening the currency
         * ---------------------------
         * The difficulty with this method is that the order table contains orders in
         * potentially different currencies, so we need to transpose all order amounts
         * into ONE currency before processing the data;
         */

        $ret = [];
        foreach ($all as $item) {

            $date = $item['date'];
            $amount = $item['sum'];
            $count = $item['count'];
            $sumNbItems = $item['sum_nbItems'];


//            if (array_key_exists($date, $ret)) {
//                $count += $ret[$date]['count'];
//                $amount += $ret[$date]['sum'];
//                $amount += $ret[$date]['sum'];
//                $sumNbItems += $ret[$date]['sumNbItems'];
//            }

            $ret[$date] = [
                'count' => $count,
                'sum' => $amount,
                'sumNbItems' => $sumNbItems,
            ];
        }


        //--------------------------------------------
        // NOW WE NEED TO FILL THE HOLES, when a date is missing, we feed it with value=0
        //--------------------------------------------
        // start by finding the date range
        if (null === $dateStart || null === $dateEnd) {


            $q = "
select 
min(date(o.date)) as min, 
max(date(o.date)) as max 
from ek_order o
where 1
            ";


            if ($qExtra) {
                $q .= $qExtra;
            }
            $minMaxDate = QuickPdo::fetch($q);


            if (null === $dateStart) {
                $dateStart = $minMaxDate['min'] . ' 00:00:00';
            }
            if (null === $dateEnd) {
                $dateEnd = $minMaxDate['max'] . ' 23:59:59';
            }
        }


        // now, filling the holes
        $dateStart = DateTool::getDate($dateStart);
        $dateEnd = DateTool::getDate($dateEnd);
        DateTool::foreachDateRange($dateStart, $dateEnd, function ($curDate) use (&$ret) {
            if (false === array_key_exists($curDate, $ret)) {
                $ret[$curDate] = [
                    "count" => 0,
                    "sum" => 0,
                    "sumNbItems" => 0,
                ];
            }
        });

        ksort($ret);
        return $ret;
    }


    public static function countFailingOrderByUserId($userId)
    {

        $userId = (int)$userId;
        $errorCodes = [
            EkomOrderStatus::STATUS_ORDER_DELIVERED_ERROR,
            EkomOrderStatus::STATUS_SHIPPING_ERROR,
            EkomOrderStatus::STATUS_PREPARING_ORDER_ERROR,
            EkomOrderStatus::STATUS_PAYMENT_ERROR,
//            EkomOrderStatus::STATUS_REIMBURSED,
//            EkomOrderStatus::STATUS_CANCELED,
        ];

        return QuickPdo::fetch("
select count(distinct(h.order_id)) as count 
from ek_order_has_order_status h 
inner join ek_order o on o.id=h.order_id
inner join ek_order_status s on s.id=h.order_status_id
where
o.user_id=$userId
and s.code in ('" . implode("', '", $errorCodes) . "')
", [], \PDO::FETCH_COLUMN);

    }

    public static function getBasicStatsByUser($userId, $table = "ek_order")
    {

        $userId = (int)$userId;
        return QuickPdo::fetch("
select 
count(id) as nb_total_order,
min(date) as min_date,
max(date) as max_date,
min(amount) as min_amount,
max(amount) as max_amount,
min(cart_quantity) as quantity_min,
max(cart_quantity) as quantity_max,
avg(cart_quantity) as quantity_avg,
round(avg(amount),2) as avg_amount,
(select count(*) as count from $table where coupon_saving = 0 and user_id=$userId) as nb_order_without_coupon

from $table
where user_id=$userId

        ");
    }

    public static function getPaymentMethodStats($userId)
    {

        $userId = (int)$userId;
        return QuickPdo::fetchAll("
select 
concat(payment_method, payment_method_extra) as payment,
count(*) as count
from ek_order
where user_id=$userId
group by payment_method, payment_method_extra

        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getRefById($id)
    {
        $id = (int)$id;
        return QuickPdo::fetch("
select reference from ek_order where id=$id        
        ", [], \PDO::FETCH_COLUMN);
    }


    /**
     * @param $id
     * @return array, the order model
     * @see EkomModels::orderModel()
     */
    public static function getOrderInfo(int $id)
    {
        if (false === array_key_exists($id, self::$orderInfoItems)) {
            $row = QuickPdo::fetch("
select * from ek_order where id=$id 
        ");
            if (false !== $row) {
                self::unserializeRow($row);
                self::$orderInfoItems[$id] = $row;
            }
        }
        return self::$orderInfoItems[$id] ?? false;
    }

    public static function getOrderLastStatus($orderId)
    {
        $orderId = (int)$orderId;
        return QuickPdo::fetch("
select s.code
from ek_order_status s 
inner join ek_order_has_order_status h on h.order_status_id=s.id

where h.order_id=$orderId
order by date desc 
limit 0, 1         
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


//    public static function getOrderLastStatusByOrderReference(string $orderReference)
//    {
//        return QuickPdo::fetch("
//select s.code
//from ek_order_status s
//inner join ek_order_has_order_status h on h.order_status_id=s.id
//inner join ek_order o on o.id=h.order_id
//
//where o.reference=:ref
//order by date asc
//        ", [
//            "ref" => $orderReference,
//        ], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
//    }


    public static function getNbOrderWithStatuses($status, $dateStart = null, $dateEnd = null)
    {

        if (!is_array($status)) {
            $status = [$status];
        }
        $markers = [];
        $arrTags = [];
        foreach ($status as $stat) {

            $markers["s" . $stat] = $stat;
            $arrTags[] = ":s" . $stat;
        }

        $sStatus = implode(', ', $arrTags);

        $q = "
select count(*) as count
from ek_order o        
where 1
        ";


        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");


        $q .= "

and  
(
  select
  s.code
  from ek_order_status s
  inner join ek_order_has_order_status h on h.order_status_id=s.id
  inner join ek_order f on f.id=h.order_id
  where order_id=o.id
  order by h.date DESC
  limit 0,1

) in ($sStatus)
        ";
        return QuickPdo::fetch($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getOrdersDistributionByRange($dateStart = null, $dateEnd = null)
    {
        $markers = [];

        $q = "

select 
label,
count(label) as nb_code 
from (

    select 
    o.id as order_id,
    (
      select
      s.label
      from ek_order_status s
      inner join ek_order_has_order_status h on h.order_status_id=s.id
      inner join ek_order f on f.id=h.order_id
      where order_id=o.id
      order by h.date DESC
      limit 0,1
    
    ) as label
    
    from ek_order o
    inner join ek_order_has_order_status hh on hh.order_id=o.id 
    inner join ek_order_status ss on ss.id=hh.order_status_id        
    where 1
        ";


        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "o.date");

        $q .= '
group by o.id

';


        $q .= "
) as any_table 
group by label

        ";

        return QuickPdo::fetchAll($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getOrderHistoryById($orderId, array $options = [])
    {
        $orderId = (int)$orderId;
        $skipIdenticalSiblings = $options['skipIdenticalSiblings'] ?? false;
        $rows = QuickPdo::fetchAll("
select 
s.code,
s.color,
s.label

from ek_order_status s 
inner join ek_order_has_order_status h on h.order_status_id=s.id

 

where h.order_id=$orderId
order by date asc         
        ");
        if (true === $skipIdenticalSiblings) {
            $prevCode = null;
            foreach ($rows as $k => $row) {
                $code = $row['code'];
                if ($code === $prevCode) {
                    unset($rows[$k]);
                }
                $prevCode = $code;
            }
        }

        return $rows;
    }


    public static function addOrderStatusByCode($orderId, $code)
    {
        $code2Ids = self::getCode2Ids();
        if (array_key_exists($code, $code2Ids)) {
            $orderStatusId = $code2Ids[$code];


            $ret = EkomApi::inst()->orderHasOrderStatus()->create([
                "order_id" => $orderId,
                "order_status_id" => $orderStatusId,
                "date" => date("Y-m-d H:i:s"),
            ]);

            if ($ret) {
                Hooks::call("Ekom_onOrderStatusUpdated", $orderId, $code);
            }

            return $ret;
        }
        throw new EkomException("Unknown code: $code");
    }


    public static function addOrderStatusById($orderId, $statusId, array $options = [])
    {
        $extra = $options['extra'] ?? "";
        $ret = EkomApi::inst()->orderHasOrderStatus()->create([
            "order_id" => $orderId,
            "order_status_id" => $statusId,
            "date" => date("Y-m-d H:i:s"),
            "extra" => $extra,
        ]);
        if ($ret) {
            $code = OrderStatusLayer::getCodeById($statusId);
            Hooks::call("Ekom_onOrderStatusUpdated", $orderId, $code);
        }
        return $ret;
    }

    public static function getCode2Ids()
    {
        return A::cache()->get("Ekom.OrderLayer.getCode2Ids", function () {
            return QuickPdo::fetchAll("select code, id from ek_order_status", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
        }, "ek_order_status");
    }

    public static function countOrders()
    {
        return QuickPdo::fetch("select count(*) as count from ek_order", [], \PDO::FETCH_COLUMN);
    }

    public static function countUserOrders($userId)
    {
        $userId = (int)$userId;
        return QuickPdo::fetch("
select count(*) as count from ek_order 
where user_id=$userId        
        ", [], \PDO::FETCH_COLUMN);
    }

    public static function getLastOrderInfoByUserId($userId)
    {
        $userId = (int)$userId;
        if (false !== ($row = QuickPdo::fetch("
select * from ek_order where user_id=$userId
order by `date` desc        
        "))) {
            self::unserializeRow($row);
            return $row;
        }
        return false;
    }


    public static function getNbUserPendingOrders($userId)
    {
        $pendingForbidden = implode(', ', StatusLayer::getPendingForbidden("'"));
        $userId = (int)$userId;
        return QuickPdo::fetch("
select

count(*) as count

from ek_order o

where
o.user_id=$userId
and 
(
  select
  s.code
  from ek_order_status s
  inner join ek_order_has_order_status h on h.order_status_id=s.id
  where order_id=o.id
  order by h.date DESC
  limit 0,1

) not in ($pendingForbidden)


", [], \PDO::FETCH_COLUMN);


    }


//    public static function getNbPreparingOrder($dateStart=null, $dateEnd=null)
//    {
//
//    }

    public static function unserializeRows(array &$rows)
    {
        foreach ($rows as $k => $row) {
            self::unserializeRow($row);
            $rows[$k] = $row;
        }
    }

    public static function unserializeRow(array &$row)
    {
        $row['user_info'] = unserialize($row['user_info']);
        $row['store_info'] = unserialize($row['store_info']);
        $row['shipping_address'] = unserialize($row['shipping_address']);
        $row['billing_address'] = unserialize($row['billing_address']);
        $row['order_details'] = unserialize($row['order_details']);
    }
}
