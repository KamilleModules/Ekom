<?php

namespace Controller\Ekom\Back\Generated\CountryLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CountryLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CountryLang_List";


        return $this->doRenderFormList([
            'title' => "Country langs",
            'breadcrumb' => "country_lang",
            'form' => "country_lang",
            'list' => "country_lang",
            'ric' => [
                'country_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Country lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}