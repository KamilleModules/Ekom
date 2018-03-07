<?php



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Country translations",
    'table' => 'ek_country_lang',
    'viewId' => 'country_translation',
    'headers' => [
        'country' => "Country",
        'country_id' => "Country id",
        'lang_id' => 'Lang Id',
        'lang' => 'Lang',
        'label' => 'Label',
        '_action' => '',
    ],
    'headersVisibility' => [
        'country_id' => false,
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'country_id' => 'c.id',
        'lang_id' => 'l.id',
        'label' => 'l.label',
        'lang_iso_code' => 'a.iso_code',
        'lang' => 'a.iso_code',
        'country' => 'c.iso_code',
    ],
    'querySkeleton' => '
select %s 
from ek_country_lang l 
inner join ek_country c on c.id=l.country_id
inner join ek_lang a on a.id=l.lang_id
',
    'queryCols' => [
        'l.country_id',
        'l.lang_id',
        'l.label',
        'c.iso_code',
        'a.iso_code as lang_iso_code',
        'concat(a.iso_code, " (", a.id, ")") as lang',
        'concat(c.iso_code, " (", c.id, ")") as country',
    ],
    'ric' => [
        'country_id',
        'lang_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_CountryTranslation_Form",
];