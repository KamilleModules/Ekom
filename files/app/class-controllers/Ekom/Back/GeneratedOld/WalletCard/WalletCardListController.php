<?php

namespace Controller\Ekom\Back\Generated\WalletCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class WalletCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_WalletCard_List";


        return $this->doRenderFormList([
            'title' => "Wallet cards",
            'breadcrumb' => "wallet_card",
            'form' => "wallet_card",
            'list' => "wallet_card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Wallet card",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}