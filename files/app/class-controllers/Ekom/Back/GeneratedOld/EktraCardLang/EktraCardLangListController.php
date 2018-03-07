<?php

namespace Controller\Ekom\Back\Generated\EktraCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraCardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraCardLang_List";


        return $this->doRenderFormList([
            'title' => "Card langs",
            'breadcrumb' => "ektra_card_lang",
            'form' => "ektra_card_lang",
            'list' => "ektra_card_lang",
            'ric' => [
                'training_card_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Card lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}