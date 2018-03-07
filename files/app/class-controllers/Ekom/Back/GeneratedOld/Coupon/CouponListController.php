<?php

namespace Controller\Ekom\Back\Generated\Coupon;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CouponListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Coupon_List";


        return $this->doRenderFormList([
            'title' => "Coupons for this shop",
            'breadcrumb' => "coupon",
            'form' => "coupon",
            'list' => "coupon",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Coupon",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}