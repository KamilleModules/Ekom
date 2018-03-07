<?php

namespace Controller\Ekom\Back\Generated\EkCoupon;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCouponListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCoupon_List";


        return $this->doRenderFormList([
            'title' => "Coupons for this shop",
            'breadcrumb' => "ek_coupon",
            'form' => "ek_coupon",
            'list' => "ek_coupon",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Coupon",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}