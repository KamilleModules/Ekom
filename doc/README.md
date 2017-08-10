


Developer useful tools
==============


- create a product
   
```php
<?php 
// Excerpt from scripts/leaderfit/trainer-create.php
// class-modules/Ekom/Api/Layer/ProductHelperLayer.php
      $res = $h->insertQuickProduct([
            "card_id" => $cId,
            "category_id" => $cat,
            "reference" => $ref,
            "weight" => 0,
            "price" => $price,
            "label" => $formationRow['NOM_FORMATION'],
            "description" => $formationRow['DESCRIPTIF_FORMATION'],
            "wholesale_price" => 0,
            "quantity" => -1,
            "slug" => $ref . "-" . date('Y-m-d__H-i-s') . '-' . $rand,
            "product_type" => null,
            "seller" => "formation",
            "attributes" => $attributes,
        ], $shopId, $langId);



```    
