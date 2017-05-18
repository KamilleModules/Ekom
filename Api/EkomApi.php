<?php


namespace Module\Ekom\Api;


use QuickPdo\QuickPdo;

class EkomApi
{

    private static $inst;


    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }


    public function getFeaturedProducts(){


        $q = '
        
select
 
 
p.id,
pl.label as title,
pl.label as imgAlt,
prs.image as imgSrc,
prs.prix_ht as price,
"" as old_price,
0 as hasPromo,
0 as hasNouveaute

from

ek_product p 
inner join ek_product_lang pl on pl.product_id=p.id
inner join ek_product_reference pr on pr.id=p.product_reference_id
inner join ek_product_reference_shop prs on prs.product_reference_id=pr.id
        
        
        
        ';


        return QuickPdo::fetchAll($q);

    }


}