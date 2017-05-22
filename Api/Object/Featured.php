<?php


namespace Module\Ekom\Api\Object;


class Featured{
    public function getFeaturedProducts(){


        $q = '
        
select
 
 
p.id,
pl.label as title,
pl.label as imgAlt,
pl.slug,
prs.image as imgSrc,
round(prs.prix_ht, 2) as price,
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