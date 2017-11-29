<?php


namespace Module\Ekom\Api\Layer;


use Authenticate\SessionUser\SessionUser;
use Bat\SessionTool;
use Core\Services\A;
use Core\Services\X;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use ListParams\ListParamsInterface;
use ListParams\Model\QueryDecorator;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\ReferenceProvider;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;

class OrderLayer
{


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


    /**
     * This is a method to filter orders.
     * It's meant to be used by me only for now (i.e. the author of ekom, not developers).
     *
     *
     * @param array $filters
     *  - ?statuses: fn
     *              bool:keepIt     fn ( array statusesHistory )
     *  - ?userId: null|int, default=null
     *                  If null, orders of all users are returned.
     *                  If int, orders of the specified user only are returned.
     *
     * @return array, the orders, filtered by statuses
     */
    public function filterOrdersByStatuses(callable $statusFilter, $userId = null)
    {
        $ret = [];
        $q = "select * from ek_order";
        if (null !== $userId) {
            $userId = (int)$userId;
            $q .= " where user_id=$userId";
        }
        $q .= " order by `date` desc";
        $rows = QuickPdo::fetchAll($q);

        $slayer = EkomApi::inst()->statusLayer();
        foreach ($rows as $row) {
            $statuses = $slayer->getStatusCodesByOrderId($row['id']);
            if (true === call_user_func($statusFilter, $statuses)) {
                $ret[] = $row;
            }
        }
        return $ret;
    }


    public function getNbUserPendingOrders($userId)
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


    public function getUniqueReference($type = 'ekom')
    {
        /**
         * @var $refProvider ReferenceProvider
         */
        $refProvider = X::get("Ekom_ReferenceProvider");
        return $refProvider->getNewReference($type);
    }


    public function getLastOrderInfoByUserId($userId)
    {
        $userId = (int)$userId;
        if (false !== ($row = QuickPdo::fetch("
select * from ek_order where user_id=$userId
order by `date` desc        
        "))) {
            $this->unserializeRow($row);
            return $row;
        }
        return false;
    }


    public function getOrderInfo($id)
    {
        $id = (int)$id;
        $userId = EkomApi::inst()->userLayer()->getUserId();

        return A::cache()->get("Ekom.OrderLayer.getOrderInfo.$id", function () use ($id, $userId) {

            $row = QuickPdo::fetch("
select * from ek_order where id=$id and user_id=$userId        
        ");
            $this->unserializeRow($row);
            return $row;
        }, [
            "ek_order.delete.$id",
            "ek_order.update.$id",
        ]);
    }


    /**
     * Used to display the orders in the front user account.
     */
    public function getUserOrderSummaries($userId, ListParamsInterface $params = null)
    {
        if ("singleAddress" === E::conf("checkoutMode")) {

            $hash = "";
            if (null !== $params) {
                $hash = $params->getHash();
            }

            $userId = (int)$userId;

            return A::cache()->get("Ekom.OrderLayer.getUserOrderSummaries.$userId.$hash", function () use ($userId, $params) {

                $pool = [];
                if (null !== $params) {
                    $pool = $params->getPool();
                }

                $status = null;
                if (array_key_exists('status', $pool)) {
                    $status = $pool['status'];
                }


                $lastOrder = $this->subLastOrderStatus();
                $q1 = "select 
o.id, 
o.reference, 
o.`date`, 
o.user_info, 
o.shipping_address, 
o.billing_address, 
o.order_details, 
$lastOrder as last_status
from ek_order o where o.user_id=$userId";


                $q2 = "select count(*) as count from ek_order o where o.user_id=$userId";


                if ('pending' === $status) {
                    $pendingForbidden = implode(', ', StatusLayer::getPendingForbidden("'"));
                    $s = " and $lastOrder not in ($pendingForbidden)";
                    $q1 .= $s;
                    $q2 .= $s;
                } elseif ('canceled' === $status) {
                    $s = " and $lastOrder='canceled'";
                    $q1 .= $s;
                    $q2 .= $s;
                }


                $markers = [];
                if (null !== $params) {
                    $nbTotalItems = QuickPdo::fetch($q2, $markers, \PDO::FETCH_COLUMN);
                    $params->setTotalNumberOfItems($nbTotalItems); // provide the nbTotalItems for the view
                }
                QueryDecorator::create()
                    ->setDefaultNipp(3)
                    ->setDefaultSort('date', 'desc')
                    ->setAllowedSortFields([
                        'id',
                        'reference',
                        'date',
                    ])
                    ->decorate($q1, $q2, $markers, $params);


                $rows = QuickPdo::fetchAll($q1);
                $ret = [];
                foreach ($rows as $k => $row) {


                    $details = unserialize($row['order_details']);
                    $section = $details['orderSections']['sections'][0];
                    $paymentDetails = (array_key_exists('payment_details', $details)) ? $details['payment_details'] : [];

                    $ret[] = [
                        "id" => $row['id'],
                        "ref" => $row['reference'],
                        "date" => $row['date'],
                        "last_status" => $row['last_status'],
                        "orderGrandTotal" => $details['orderGrandTotal'],
                        "paymentMethodName" => $details['paymentMethodName'],
                        "payment_details" => $paymentDetails,
                        "shipping_address" => unserialize($row['shipping_address']),
                        "billing_address" => unserialize($row['billing_address']),
                        "user_info" => unserialize($row['user_info']),
                        "section" => $section,
                    ];


                }
                return $ret;
            }, [
                "ek_order",
                "ek_order_status",
                "ek_order_has_order_status",
            ]);
        }
        throw new \Exception("Not implemented yet with checkoutMode " . E::conf("checkoutMode"));
    }


    /**
     * model for userAccountOrderItem:
     *
     *
     *
     */
    public function getUserAccountOrderItems($userId, ListParamsInterface $params = null)
    {
        $userId = (int)$userId;

        $q = "select id, reference, `date`, order_details from ek_order where user_id=$userId";
        $q2 = "select count(*) as count from ek_order where user_id=$userId";


        $markers = [];
        QueryDecorator::create()
            ->setAllowedSearchFields([
                'id',
                'reference',
                'date',
            ])
            ->setAllowedSortFields([
                'id',
                'reference',
                'date',
            ])
            ->decorate($q, $q2, $markers, $params);

        $nbTotalItems = QuickPdo::fetch($q2, $markers, \PDO::FETCH_COLUMN);
        $params->setTotalNumberOfItems($nbTotalItems); // provide the nbTotalItems for the view
        $rows = QuickPdo::fetchAll($q, $markers);


        $ret = [];
        foreach ($rows as $k => $row) {

//                $rows[$k]['user_info'] = unserialize($row['user_info']);
//                $rows[$k]['shop_info'] = unserialize($row['shop_info']);
//                $rows[$k]['shipping_address'] = unserialize($row['shipping_address']);
//                $rows[$k]['billing_address'] = unserialize($row['billing_address']);
            $details = unserialize($row['order_details']);
//                az($details);
            $products = [];
            $section = $details['orderSections']['sections'][0];
            $pInfo = $section['productsInfo'];
            foreach ($pInfo as $p) {
                $products[] = [
                    "label" => $p['label'],
                    "ref" => $p['ref'],
                    "seller" => $p['seller'],
                    "uri" => $p['uri_card_with_ref'],
                    "quantity" => $p['quantity'],
                    "linePrice" => $p['linePrice'],
                    "image" => $p['image'],
                ];
            }


            $ret[] = [
                "id" => $row['id'],
                "ref" => $row['reference'],
                "date" => $row['date'],
                "orderGrandTotal" => $details['orderGrandTotal'],
                "products" => $products,
            ];


        }
        // hooks decorate rows?
        return $ret;

    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function unserializeRow(array &$row)
    {
        $row['user_info'] = unserialize($row['user_info']);
        $row['shop_info'] = unserialize($row['shop_info']);
        $row['shipping_address'] = unserialize($row['shipping_address']);
        $row['billing_address'] = unserialize($row['billing_address']);
        $row['order_details'] = unserialize($row['order_details']);
    }

    private function subLastOrderStatus($orderField = null)
    {
        if (null === $orderField) {
            $orderField = 'o.id';
        }
        return "
(
  select
  s.code
  from ek_order_status s
  inner join ek_order_has_order_status h on h.order_status_id=s.id
  where order_id=$orderField
  order by h.date DESC
  limit 0,1
)
        ";
    }
}
