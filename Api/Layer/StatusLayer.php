<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use SaveOrmObject\Object\Ek\OrderStatusLangObject;
use SaveOrmObject\Object\Ek\OrderStatusObject;

class StatusLayer
{


    /**
     * @param array $statuses
     *      - label
     *      - code
     *      - color
     */
    public function createStatuses(array $statuses, $cleanAll = false, $shopId = null, $langId = null)
    {
        $shopId = E::getShopId($shopId);
        $langId = E::getShopId($langId);

        if (true === $cleanAll) {
            QuickPdo::delete("ek_order_status", [
                ["shop_id", '=', $shopId],
            ]);
        }

        foreach ($statuses as $status) {
            $code = $status['code'];
            $color = array_key_exists('color', $status) ? $status['color'] : $this->getRandomColor();
            if (array_key_exists('label', $status)) {
                $label = $status['label'];
            } else {
                $label = ucfirst(implode(' ', explode('_', $code)));
            }

            OrderStatusObject::createByCodeShopId($code, $shopId)
                ->setColor($color)
                ->createOrderStatusLang(OrderStatusLangObject::createUpdate()
                    ->setLangId($langId)
                    ->setLabel($label)
                )
                ->save();
        }

    }

    public function getStatusCodesByOrderId($orderId)
    {
        return QuickPdo::fetchAll("
select s.code 
from ek_order_status s 
inner join ek_order_has_order_status h on h.order_status_id=s.id 
where h.order_id=$orderId 
order by h.date desc       
        ", [], \PDO::FETCH_COLUMN);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
//    public static function isPending(array $statuses){
//        return (
//            false === in_array('order_delivered', $statuses) &&
//            false === in_array('canceled', $statuses) &&
//            false === in_array('reimbursed', $statuses)
//        );
//    }

    public static function getPendingForbidden($wrapWithQuotes = null)
    {
        $statuses = [
            'order_delivered',
            'canceled',
            'reimbursed',
        ];
        if (null === $wrapWithQuotes) {
            return $statuses;
        }
        $q = $wrapWithQuotes;

        $ret = [];
        foreach ($statuses as $status) {
            $ret[] = $q . $status . $q;
        }
        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getRandomColor()
    {
        return '#' . substr(md5(rand()), 0, 6);
    }
}
