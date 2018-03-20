<?php


namespace Module\Ekom\Models\Product\AttributeList;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Models\Iterator\IteratorTrait;
use Module\Ekom\Models\Product\Attribute\AttributeModel;
use QuickPdo\QuickPdo;


// http://php.net/manual/fr/class.iterator.php#90830
class AttributeListModel implements \Iterator
{

    use IteratorTrait;
    private $myArray;


    //--------------------------------------------
    //
    //--------------------------------------------
    public static function createByProductId($productId, $langId = null)
    {

        if (null === $langId) {

            $langId = ApplicationRegistry::get("ekom.lang_id");
        }


        $rows = QuickPdo::fetchAll("
select 

a.id as name_id,
a.name,
al.name as attribute_label,

v.id as value_id,
v.value,
vl.value as value_label


from ek_product_has_product_attribute h

inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_lang al on al.product_attribute_id=a.id 
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id 
inner join ek_product_attribute_value_lang vl on vl.product_attribute_value_id=v.id 

where al.lang_id=$langId 
and vl.lang_id=$langId
and product_id = $productId
         
order by h.order asc         
         
");

        $arr = [];
        foreach ($rows as $row) {
            $arr[] =
                AttributeModel::create()
                    ->setNameId($row['name_id'])
                    ->setName($row['name'])
                    ->setNameLabel($row['attribute_label'])
                    //
                    ->setValueId($row['value_id'])
                    ->setValue($row['value'])
                    ->setValueLabel($row['value_label']);
        }
        return new static($arr);
    }



}