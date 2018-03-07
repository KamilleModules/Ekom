<?php

namespace Controller\Ekom\Back\Generated\EkTaxLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkTaxLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkTaxLang_List";


        return $this->doRenderFormList([
            'title' => "Tax langs",
            'breadcrumb' => "ek_tax_lang",
            'form' => "ek_tax_lang",
            'list' => "ek_tax_lang",
            'ric' => [
                'tax_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Tax lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}