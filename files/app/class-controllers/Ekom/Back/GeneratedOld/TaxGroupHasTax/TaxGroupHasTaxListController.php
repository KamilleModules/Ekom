<?php

namespace Controller\Ekom\Back\Generated\TaxGroupHasTax;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TaxGroupHasTaxListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TaxGroupHasTax_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$tax_group_id = $this->getContextFromUrl('tax_group_id');
		$table = "ek_tax_group_has_tax";
		$context = [
			"tax_group_id" => $tax_group_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_tax_group");
            $avatar = QuickPdo::fetch("
select $repr from `ek_tax_group` where id=:tax_group_id 
            ", [
				"tax_group_id" => $tax_group_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Taxes for tax group \"$avatar\"",
            'breadcrumb' => "tax_group_has_tax",
            'form' => "tax_group_has_tax",
            'list' => "tax_group_has_tax",
            'ric' => [
                'tax_group_id',
                'tax_id',
            ],
            
            "newItemBtnText" => "Add a new tax for tax group \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TaxGroupHasTax_List") . "?form&tax_group_id=$tax_group_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTaxGroup_List",             
            "buttons" => [
                [
                    "label" => "Back to tax group \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_TaxGroup_List") . "?id=$tax_group_id",
                ],
            ],
            "context" => [
            	"tax_group_id" => $tax_group_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}