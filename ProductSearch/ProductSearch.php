<?php


namespace Module\Ekom\ProductSearch;


use Bat\FileSystemTool;
use Bat\StringTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class ProductSearch extends AbstractProductSearch
{


    /**
     * @param $query
     * @return array, each entry being an entry with the following keys:
     *          - value: the label to display
     *          - data: the uri to the product or product card
     *
     */
    protected function doGetResults($query)
    {


        EkomApi::inst()->initWebContext();
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");


        return A::cache()->get("Ekom.ProductSearch.getResults.$shopId.$langId.$query", function () use ($query, $shopId, $langId) {


            $rows = QuickPdo::fetchAll("
select 
p.label as product_label,
ll.label as product_label_default,
b.reference,
c.label as product_card_label,
l.label as product_card_label_default,

p.slug as product_slug,
c.slug as card_slug,            
l.slug as card_default_slug,
l.product_card_id,
b.reference
            
from ek_shop_has_product_lang p            
inner join ek_product b on b.id=p.product_id
inner join ek_product_lang ll on ll.product_id=b.id
inner join ek_product_card_lang l on l.product_card_id=b.product_card_id and l.lang_id=p.lang_id
inner join ek_shop_has_product_card_lang c on c.shop_id=p.shop_id and c.product_card_id=b.product_card_id and c.lang_id=p.lang_id            
            
where 
p.shop_id=$shopId            
and p.lang_id=$langId
and ( 
p.label like :query or 
ll.label like :query or 
b.reference like :query or 
l.label like :query or 
c.label like :query
)       


group by b.reference  
      
     

            ", [
                "query" => '%' . str_replace('%', '\%', $query) . '%',
            ]);


            $ret = [];
            $c = 0;
            foreach ($rows as $row) {

                $cardSlug = ('' !== $row['card_slug']) ? $row['card_slug'] : $row['card_default_slug'];


                $label = "";
                if ('' !== $row['product_label']) {
                    $label = $row['product_label'];
                } elseif ('' !== $row['product_label_default']) {
                    $label = $row['product_label_default'];
                } elseif ('' !== $row['product_card_label']) {
                    $label = $row['product_card_label'];
                } elseif ('' !== $row['product_card_label_default']) {
                    $label = $row['product_card_label_default'];
                }

                $value = '';
                if ('' === $label) {
                    $value = $row['reference'];
                } else {
                    $value = $label . ' (' . $row['reference'] . ')';
                }


                $ret[] = [
                    'value' => $value,
                    'pokemon' => "pokepou" . $c++,
                    'data' => E::link("Ekom_productCardRef", ['slug' => $cardSlug, 'ref' => $row["reference"]]),
                ];
            }


            return $ret;


        }, [
            "ek_shop_has_product_lang",
            "ek_product",
            "ek_product_lang",
            "ek_product_card_lang",
            "ek_shop_has_product_card_lang",
        ]);
    }
}

