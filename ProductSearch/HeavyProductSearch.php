<?php


namespace Module\Ekom\ProductSearch;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class HeavyProductSearch extends AbstractProductSearch
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


        
        $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        $langId = (int)ApplicationRegistry::get("ekom.lang_id");


        return A::cache()->get("Ekom.HeavyProductSearch.getResults.$shopId.$langId.$query", function () use ($query, $shopId, $langId) {


            $ids = QuickPdo::fetchAll("
select b.id
            
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
            ], \PDO::FETCH_COLUMN);


            $pLayer = EkomApi::inst()->productLayer();


            $ret = [];
            foreach ($ids as $id) {

                $item = [];
                $model = $pLayer->getProductBoxModelByProductId($id, $shopId, $langId);
                $item['imageThumb'] = $model['imageThumb'];
                $item['label'] = $model['label'];
                $item['ref'] = $model['ref'];
                $item['salePrice'] = $model['salePrice'];

                //
                $item['value'] = $model['label'] . '( ' . $model['ref'] . ' )';
                $item['data'] = $model['uriCard'];

                $ret[] = $item;
            }

            return $ret;
        }, [
            "ek_shop_has_product_lang",
            "ek_product",
            "ek_product_lang",
            "ek_shop_has_product_card_lang",
            "ek_shop_has_product_card",
            "ek_product_card_lang",
            "ek_product_card",
            "ek_shop",
            "ek_product_has_product_attribute",
            "ek_product_attribute_lang",
            "ek_product_attribute_value_lang",
            "ekomApi.image.product",
            "ekomApi.image.productCard",
        ]);
    }
}

