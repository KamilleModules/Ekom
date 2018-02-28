<?php


namespace Module\Ekom\Api\Layer;


use Bat\DateTool;
use Core\Services\A;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Util\CurrencyUtil;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Status\EkomOrderStatus;
use Module\Ekom\Utils\E;
use QuickPdo\Helper\QuickPdoHelper;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use XiaoApi\Helper\QuickPdoStmtHelper\QuickPdoStmtHelper;

class OrderLayer
{


    public static function getOrdersAmountAndCountByDate($dateStart = null, $dateEnd = null, $shopId = null)
    {
        $markers = [];
        $q = "select date(date) as date, currency_iso_code, sum(amount) as sum, count(*) as count  from ek_order where 1";
        if (null !== $shopId) {
            $q .= " and shop_id=" . (int)$shopId;
        }


        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");
        $q .= " group by date(date)";
        $all = QuickPdo::fetchAll($q, $markers);
        return $all;
    }


    /**
     * @param null $dateStart
     * @param null $dateEnd
     * @param null|false|string $currencyIso
     *          if false, return an array
     *
     * @return array, depends on the value of currencyIso:
     *          if currencyIso is false, then return an array of currencyIsoCode => amount
     *          else,
     *              return the total amount for the date range in the given currency iso code
     *
     */
    public static function getOrdersAmountAndCount($dateStart = null, $dateEnd = null, $currencyIso = null, array $options = [])
    {

        $options = array_replace([
            'queryWhereExtra' => '',
            'queryWhereExtraMarkers' => [],
            'shopId' => null,
        ], $options);

        $qExtra = $options['queryWhereExtra'];
        $qExtraMarkers = $options['queryWhereExtraMarkers'];
        $shopId = $options['shopId'];


        $q = "select currency_iso_code, sum(amount) as sum, count(*) as count  from ek_order where 1";
        if (null !== $shopId) {
            $q .= " and shop_id=" . (int)$shopId;
        }

        $markers = $qExtraMarkers;
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");
        if ($qExtra) {
            $q .= $qExtra;
        }
        $q .= " group by currency_iso_code";


        $all = QuickPdo::fetchAll($q, $markers);


        $defaultCurrencyIso = E::getCurrencyIso();
        if (null === $currencyIso) {
            $currencyIso = $defaultCurrencyIso;
        }


        /**
         * Flattening the currency
         * ---------------------------
         * The difficulty with this method is that the order table contains orders in
         * potentially different currencies, so we need to transpose all order amounts
         * into ONE currency before processing the data;
         */

        $total = 0;
        $countTotal = 0;
        foreach ($all as $item) {

            $currency = $item['currency_iso_code'];
            $amount = $item['sum'];
            $count = $item['count'];


            // count
            $countTotal += $count;

            // sum
            if ($currencyIso === $currency) {
                $total += $amount;
            } else {
                if ("" === $currency) {
                    $currency = $defaultCurrencyIso;
                }
                if ($currencyIso !== $currency) {
                    $amount = CurrencyUtil::convertAmount($amount, $currency, $currencyIso);
                }
                $total += $amount;
            }
        }
        return [
            $total, // sum
            $countTotal, // count
        ];

    }


    public static function getOrdersAmountAndCountGraph($dateStart = null, $dateEnd = null, $currencyIso = null, array $options = [])
    {

        $options = array_replace([
            'queryWhereExtra' => '',
            'queryWhereExtraMarkers' => [],
            'shopId' => null,
        ], $options);

        $qExtra = $options['queryWhereExtra'];
        $qExtraMarkers = $options['queryWhereExtraMarkers'];
        $shopId = $options['shopId'];


        $q = "select date(date) as date, currency_iso_code, sum(amount) as sum, count(*) as count  from ek_order where 1";
        if (null !== $shopId) {
            $q .= " and shop_id=" . (int)$shopId;
        }

        $markers = $qExtraMarkers;
        QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "date");
        if ($qExtra) {
            $q .= $qExtra;
        }
        $q .= " group by date(date), currency_iso_code";


        $all = QuickPdo::fetchAll($q, $markers);


        $defaultCurrencyIso = E::getCurrencyIso();
        if (null === $currencyIso) {
            $currencyIso = $defaultCurrencyIso;
        }


        /**
         * Flattening the currency
         * ---------------------------
         * The difficulty with this method is that the order table contains orders in
         * potentially different currencies, so we need to transpose all order amounts
         * into ONE currency before processing the data;
         */

        $ret = [];
        foreach ($all as $item) {

            $date = $item['date'];
            $currency = $item['currency_iso_code'];
            $amount = $item['sum'];
            $count = $item['count'];

            // converting amount into the right currency
            if ($currencyIso !== $currency) {
                if ("" === $currency) {
                    $currency = $defaultCurrencyIso;
                }
                if ($currencyIso !== $currency) {
                    $amount = CurrencyUtil::convertAmount($amount, $currency, $currencyIso);
                }
            }


            if (array_key_exists($date, $ret)) {
                $count += $ret[$date]['count'];
                $amount += $ret[$date]['sum'];
            }

            $ret[$date] = [
                'count' => $count,
                'sum' => $amount,
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

            if (null !== $shopId) {
                $q .= " and shop_id=" . (int)$shopId;
            }


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
                ];
            }
        });

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

    public static function getOrderInfo($id)
    {
        /**
         * @todo-ling, remove force generate
         */
        $id = (int)$id;
        return A::cache()->get("Ekom.OrderLayer.getOrderInfo.$id.", function () use ($id) {

            $row = QuickPdo::fetch("
select * from ek_order where id=$id 
        ");
            if (false !== $row) {
                self::unserializeRow($row);
            }
            return $row;
        }, true);
    }

    public static function getOrderLastStatus($orderId)
    {
        $orderId = (int)$orderId;
        return QuickPdo::fetch("
select s.code
from ek_order_status s 
inner join ek_order_has_order_status h on h.order_status_id=s.id

where h.order_id=$orderId
order by date asc         
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getNbOrderWithStatuses($status, $dateStart = null, $dateEnd = null, $shopId = null)
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

        if (null !== $shopId) {
            /**
             *  https://stackoverflow.com/questions/49028189/mysql-nested-query-not-working-with-an-and-condition
             */
            $shopId = (int)$shopId;
            $q .= "
AND EXISTS (SELECT 1 FROM ek_order WHERE shop_id=$shopId)            
            ";
        }

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


    public static function getOrderHistoryById($orderId, $langId = null)
    {
        $langId = E::getLangId($langId);
        $orderId = (int)$orderId;
        return QuickPdo::fetchAll("
select 
s.code,
s.color,
l.label

from ek_order_status s 
inner join ek_order_has_order_status h on h.order_status_id=s.id
inner join ek_order_status_lang l on l.order_status_id=s.id
 

where h.order_id=$orderId
and l.lang_id=$langId
order by date asc         
        ");
    }


    public static function addOrderStatusByCode($orderId, $code, $shopId = null)
    {
        $shopId = E::getShopId($shopId);
        $code2Ids = self::getCode2Ids($shopId);
        if (array_key_exists($code, $code2Ids)) {
            $orderStatusId = $code2Ids[$code];
            return EkomApi::inst()->orderHasOrderStatus()->create([
                "order_id" => $orderId,
                "order_status_id" => $orderStatusId,
                "date" => date("Y-m-d H:i:s"),
            ]);
        }
        throw new EkomException("Unknown code: $code");
    }

    public static function getCode2Ids($shopId)
    {
        return A::cache()->get("Ekom.OrderLayer.$shopId", function () use ($shopId) {
            return EkomApi::inst()->orderStatus()->readKeyValues("code", "id", [
                "where" => [
                    ["shop_id", "=", $shopId],
                ],
            ]);
        });
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
        $row['shop_info'] = unserialize($row['shop_info']);
        $row['shipping_address'] = unserialize($row['shipping_address']);
        $row['billing_address'] = unserialize($row['billing_address']);
        $row['order_details'] = unserialize($row['order_details']);
    }
}
