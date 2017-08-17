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
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\ReferenceProvider;
use OnTheFlyForm\Provider\OnTheFlyFormProviderInterface;
use QuickPdo\QuickPdo;

class OrderLayer
{


    public function getUniqueReference()
    {
        /**
         * @var $refProvider ReferenceProvider
         */
        $refProvider = X::get("Ekom_ReferenceProvider");
        return $refProvider->getNewReference();
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

    public function addOrderStatusByEkomAction($orderId, $ekomAction)
    {

        if (false !== ($code = X::get("Ekom_StatusProvider")->getCode($ekomAction))) {


            EkomApi::inst()->initWebContext();
            $shopId = ApplicationRegistry::get("ekom.shop_id");
            $code2Ids = $this->getCode2Ids($shopId);
            if (array_key_exists($code, $code2Ids)) {
                $orderStatusId = $code2Ids[$code];
                return EkomApi::inst()->orderHasOrderStatus()->create([
                    "order_id" => $orderId,
                    "order_status_id" => $orderStatusId,
                    "date" => date("Y-m-d H:i:s"),
                ]);
            }
            XLog::error("[Ekom module] - OrderLayer: code not found ($code) for shop $shopId");
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


    public function getUserOrderSummaries($userId)
    {
        if ("singleAddress" === E::conf("checkoutMode")) {


            $userId = (int)$userId;

            return A::cache()->get("Ekom.OrderLayer.getUserOrderSummaries.$userId", function () use ($userId) {

                $rows = QuickPdo::fetchAll("
select id, reference, `date`, order_details from ek_order where user_id=$userId        
        ");
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
                return $ret;
            }, [
                "ek_order",
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


    public function countUserOrders($userId)
    {
        $ret = 0;
        $userId = (int)$userId;
        if (false !== ($row = QuickPdo::fetch("
select count(*) as count from ek_order 
where user_id=$userId        
        "))
        ) {
            $ret = (int)$row['count'];
        }
        return $ret;
    }

    //--------------------------------------------
    //
    //--------------------------------------------

    public function getCode2Ids($shopId)
    {
        return A::cache()->get("Ekom.OrderLayer.$shopId", function () use ($shopId) {
            return EkomApi::inst()->orderStatus()->readKeyValues("code", "id", [
                "where" => [
                    ["shop_id", "=", $shopId],
                ],
            ]);
        }, [
            "ek_order_status.create",
            "ek_order_status.delete.$shopId",
            "ek_order_status.update.$shopId",
        ]);
    }


    private function unserializeRow(array &$row)
    {
        $row['user_info'] = unserialize($row['user_info']);
        $row['shop_info'] = unserialize($row['shop_info']);
        $row['shipping_address'] = unserialize($row['shipping_address']);
        $row['billing_address'] = unserialize($row['billing_address']);
        $row['order_details'] = unserialize($row['order_details']);
    }
}
