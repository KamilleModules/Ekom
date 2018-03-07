<?php

namespace Controller\Ekom\Back\Generated\EkCouponLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCouponLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCouponLang_List";


        return $this->doRenderFormList([
            'title' => "Coupon langs",
            'breadcrumb' => "ek_coupon_lang",
            'form' => "ek_coupon_lang",
            'list' => "ek_coupon_lang",
            'ric' => [
                'lang_id',
                'coupon_id',
            ],
            
            "newItemBtnText" => "Add a new Coupon lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}