<?php

namespace Controller\Ekom\Back\Generated\CardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CardLang_List";


        return $this->doRenderFormList([
            'title' => "Card langs",
            'breadcrumb' => "card_lang",
            'form' => "card_lang",
            'list' => "card_lang",
            'ric' => [
                'training_card_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Card lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}