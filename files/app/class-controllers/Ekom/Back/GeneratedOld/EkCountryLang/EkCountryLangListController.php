<?php

namespace Controller\Ekom\Back\Generated\EkCountryLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCountryLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCountryLang_List";


        return $this->doRenderFormList([
            'title' => "Country langs",
            'breadcrumb' => "ek_country_lang",
            'form' => "ek_country_lang",
            'list' => "ek_country_lang",
            'ric' => [
                'country_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Country lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}