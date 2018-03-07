<?php

namespace Controller\Ekom\Back\Generated\TaxLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TaxLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TaxLang_List";


        return $this->doRenderFormList([
            'title' => "Tax langs",
            'breadcrumb' => "tax_lang",
            'form' => "tax_lang",
            'list' => "tax_lang",
            'ric' => [
                'tax_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Tax lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}