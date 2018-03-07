<?php

namespace Controller\Ekom\Back\Generated\PeiWalletCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PeiWalletCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_PeiWalletCard_List";


        return $this->doRenderFormList([
            'title' => "Wallet cards",
            'breadcrumb' => "pei_wallet_card",
            'form' => "pei_wallet_card",
            'list' => "pei_wallet_card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Wallet card",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}