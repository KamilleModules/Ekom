<?php

namespace Controller\Ekom\Back\Generated\CouponLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CouponLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CouponLang_List";


        return $this->doRenderFormList([
            'title' => "Coupon langs",
            'breadcrumb' => "coupon_lang",
            'form' => "coupon_lang",
            'list' => "coupon_lang",
            'ric' => [
                'lang_id',
                'coupon_id',
            ],
            
            "newItemBtnText" => "Add a new Coupon lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}