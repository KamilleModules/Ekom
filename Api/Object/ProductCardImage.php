<?php


namespace Module\Ekom\Api\Object;


use Module\Ekom\Api\GeneratedObject\GeneratedProductCardImage;
use QuickPdo\QuickPdo;


class ProductCardImage extends GeneratedProductCardImage
{
    public function __construct()
    {
        parent::__construct();

        $this->addListener([
            'createAfter',
            'updateAfter',

        ], function ($eventName, $table, $data, $thing) {
            /**
             * If an image is the default image, then all other images of the card must be set to 0.
             */
            if (array_key_exists("is_default", $data) && 1 === (int)$data['is_default']) {
                if (array_key_exists('product_card_id', $data)) {
                    $product_card_id = (int)$data['product_card_id'];
                    $productId = null;
                    if ("updateAfter" === $eventName && is_array($thing)) {
                        if (array_key_exists('id', $thing)) {
                            $productId = (int)$thing["id"];
                        }
                    } elseif ("createAfter" === $eventName) {
                        if ($thing) {
                            $productId = (int)$thing;
                        }
                    }

                    if (null !== $productId) {
                        QuickPdo::update("ek_product_card_image", ['is_default' => 0], [
                            ["product_card_id", "=", $product_card_id],
                            ["id", "!=", $productId],
                        ]);
                    }

                }
            }
        });


        $this->addListener('deleteBefore', function ($eventName, $table, $where) {
            if (array_key_exists("id", $where)) {
                $deletedImageId = (int)$where['id'];
                $q = "
select 
i.id,
i.is_default 
from ek_product_card_image i 
inner join ek_product_card_image i2 on i2.product_card_id=i.product_card_id 
where 
i.id!=$deletedImageId
and i2.id=$deletedImageId
order by i.`position` asc
";

                $id2Default = QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
                $found = false;
                foreach ($id2Default as $imgId => $isDefault) {
                    if (1 === (int)$isDefault) {
                        $found = true;
                    }
                }

                if (false === $found) {
                    reset($id2Default);
                    $imageId = key($id2Default);
                    if ($imageId) {
                        QuickPdo::update("ek_product_card_image", [
                            "is_default" => 1,
                        ], [
                            ["id", "=", $imageId],
                        ]);
                    }
                }
            }
        });
    }


}