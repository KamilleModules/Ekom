<?php


namespace Module\Ekom\Models\AttributeList;


use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Models\Attribute\AttributeModel;
use QuickPdo\QuickPdo;


// http://php.net/manual/fr/class.iterator.php#90830
class AttributeListModel implements \Iterator
{

    private $myArray;


    //--------------------------------------------
    //
    //--------------------------------------------
    public static function createByProductId($productId, $langId = null)
    {

        if (null === $langId) {
            EkomApi::inst()->initWebContext();
            $langId = ApplicationRegistry::get("ekom.lang_id");
        }


        $rows = QuickPdo::fetchAll("
select 

a.id as name_id,
a.name,
al.name as name_label,

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
                    ->setNameLabel($row['name_label'])
                    //
                    ->setValueId($row['value_id'])
                    ->setValue($row['value'])
                    ->setValueLabel($row['value_label']);
        }
        return new static($arr);
    }









    //--------------------------------------------
    // Iterator
    //--------------------------------------------
    public function __construct($givenArray)
    {
        $this->myArray = $givenArray;
    }

    public function rewind()
    {
        return reset($this->myArray);
    }

    public function current()
    {
        return current($this->myArray);
    }

    public function key()
    {
        return key($this->myArray);
    }

    public function next()
    {
        return next($this->myArray);
    }

    public function valid()
    {
        return key($this->myArray) !== null;
    }


}