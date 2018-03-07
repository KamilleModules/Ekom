<?php


use Module\Ekom\Api\Layer\TaxLayer;
use Module\Ekom\Utils\E;


$taxId = array_key_exists("id", $_GET) ? (int)$_GET['id'] : 0;
if (0 === $taxId) {
    if (array_key_exists("tax_id", $_GET)) {
        $taxId = (int)$_GET['tax_id'];
    }
}

if (0 === $taxId) {
    if (array_key_exists("tax_id", $context)) {
        $taxId = (int)$context['tax_id'];
    }
    elseif (array_key_exists("id", $context)) {
        $taxId = (int)$context['id'];
    }
}


if (0 !== $taxId) {


    $q = "select %s 
from ek_tax_lang l 
inner join ek_tax t on t.id=l.tax_id
inner join ek_lang a on a.id=l.lang_id";
    if (0 !== $taxId) {
        $q .= "
where t.id=$taxId    
    ";
    }


    $label = "Tax translations";

    $amount = TaxLayer::getTaxAmountById($taxId);
    $label .= " for tax amount: $amount";
    $link = E::link("NullosAdmin_Ekom_TaxTranslation_List") . "?id=" . $taxId;


    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => $label,
        'table' => 'ek_tax_lang',
        'viewId' => 'tax_translation',
        'headers' => [
            'tax_id' => "Tax id",
            'lang_id' => 'Lang Id',
            'amount' => "Amount",
            'label' => 'Label',
            'lang' => 'Lang',
            '_action' => '',
        ],
        'headersVisibility' => [
            'tax_id' => false,
            'lang_id' => false,
        ],
        'realColumnMap' => [
            'tax_id' => 't.id',
            'lang_id' => 'l.id',
            'label' => 'l.label',
            'lang_iso_code' => 'a.iso_code',
            'lang' => 'a.iso_code',
            'tax' => 't.amount',
        ],
        'querySkeleton' => $q,
        'queryCols' => [
            'l.tax_id',
            'l.lang_id',
            'l.label',
            't.amount',
            'concat(a.iso_code, " (", a.id, ")") as lang',
        ],
        'ric' => [
            'tax_id',
            'lang_id',
        ],
        /**
         * formRoute is just a helper, it will be used to generate the rowActions key.
         */
        'formRoute' => "NullosAdmin_Ekom_TaxTranslation_List",
        'context' => $context,
        'buttons' => [
            [
                'link' => $link,
                'text' => "Ajouter une traduction pour cette taxe",
            ],
        ],
    ];


} else {
    throw new \Exception("Tax id not set");
}
