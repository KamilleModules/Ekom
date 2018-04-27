<?php


namespace Module\Ekom\Api\Layer;


use Bat\FileSystemTool;
use Bat\StringTool;
use Bat\UriTool;
use Core\Services\A;
use Core\Services\Hooks;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\UserHasCoupon;
use Module\Ekom\Helper\ConditionRulesHelper;
use Module\Ekom\Model\EkomModel;
use Module\Ekom\Models\EkomModels;
use Module\Ekom\SokoForm\Controls\SokoCouponRulesFreeHtmlControl;
use Module\Ekom\Utils\E;
use Module\ThisApp\Ekom\Helper\CartHelper;
use QuickPdo\QuickPdo;


class UserHasCouponLayer
{


    public static function getNbCouponsByCouponIdUserId(int $couponId, int $userId)
    {
        return QuickPdo::fetch("
select count(*) as count 
from ek_user_has_coupon 
where user_id=$userId
and coupon_id=$couponId
", [], \PDO::FETCH_COLUMN);
    }

    public static function addUserHasCouponEntry(int $userId, int $couponId)
    {
        UserHasCoupon::getInst()->create([
            "user_id" => $userId,
            "coupon_id" => $couponId,
            "date_added" => date("Y-m-d H:i:s"),
        ]);
    }


    public static function decrementUserCouponIfNecessary(int $userId, int $couponId)
    {
        $quantityInfo = QuickPdo::fetch("
select 
quantity_per_user,
(select count(*) as count from ek_user_has_coupon where user_id=$userId) as current_quantity

from ek_coupon
where id=$couponId
");
        if (false !== $quantityInfo) {

            $quantityPerUser = $quantityInfo['quantity_per_user'];
            $currentQuantity = $quantityInfo['current_quantity'];

            if ($quantityPerUser > $currentQuantity) {
                self::addUserHasCouponEntry($userId, $couponId);
            }
        }
    }
}

