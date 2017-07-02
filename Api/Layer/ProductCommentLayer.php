<?php


namespace Module\Ekom\Api\Layer;


use Bat\UriTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use RowsGenerator\QuickPdoRowsGenerator;

class ProductCommentLayer
{

    /**
     * Use this method when the user tries to insert a comment for a product on the website.
     */
    public function insertComment($productId, array $data, $userId = null, $shopId = null)
    {
        if (null === $userId) {
            $userId = E::getUserId();
        }
        if (null === $shopId) {
            EkomApi::inst()->initWebContext();
            $shopId = (int)ApplicationRegistry::get("ekom.shop_id");
        }

        $title = (array_key_exists('title', $data)) ? $data['comment'] : '';

        $commentNeedValidation = XConfig::get("Ekom.commentNeedValidation");
        $commentModeratorEmail = XConfig::get("Ekom.commentModeratorEmail");
        $active = 1;
        if (true === $commentNeedValidation) {
            $active = 0;
        }


        $date = date('Y-m-d H:i:s');
        $commentId = QuickPdo::insert("ek_product_comment", [
            'shop_id' => $shopId,
            'product_id' => $productId,
            'user_id' => $userId,
            'date' => $date,
            'rating' => $data['rating'],
            'useful_counter' => 0,
            'title' => $title,
            'comment' => $data['comment'],
            'active' => $active,
        ]);

        $userInfo = EkomApi::inst()->userLayer()->getUserInfo($userId);
        $userEmail = $userInfo['email'];
        $boxModel = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($productId);

        $link = $boxModel['uriCard'];
        $link = UriTool::uri($link, [], true, true);


        if (true === $commentNeedValidation) {


            // send email to moderator
            if (false === E::sendMail("commentAwaitsModeration", [
                    "to" => $commentModeratorEmail,
                    "subject" => "{siteName}: a comment awaits your moderation",
                    "commonVars" => [
                        'productLabel' => $boxModel['label'],
                        'productRef' => $boxModel['ref'],
                        'productUri' => $link,
                        'title' => $title,
                        'comment' => $data['comment'],
                        'date' => $date,
                    ],
                ])
            ) {
                XLog::error("[Ekom module] - ProductCommentLayer: couldn't send commentAwaitsModeration email to $commentModeratorEmail");
            }


            // send email to user
            if (false === E::sendMail("yourCommentAwaitsModeration", [
                    "subject" => "{siteName}: your comment awaits moderation",
                    "to" => $userEmail,
                ])
            ) {
                XLog::error("[Ekom module] - ProductCommentLayer: couldn't send YourCommentAwaitsModeration email to $userEmail");
            }

        } else {
            // send email to user
            if (false === E::sendMail("yourCommentHasBeenApproved", [
                    "to" => $userEmail,
                    "subject" => "{siteName}: your comment has been approved",
                    'commonVars' => [
                        'productLabel' => $boxModel['label'],
                        'productRef' => $boxModel['ref'],
                        'productUri' => $link,
                    ],
                ])
            ) {
                XLog::error("[Ekom module] - ProductCommentLayer: couldn't send yourCommentHasBeenApproved email to $userEmail");
            }
        }

        return $commentId;
    }


    public function getCommentsByProductId($productId, $shopId = null, array $options = [])
    {

        $options = array_replace([
            'page' => 1,
            'sort' => 'date',
            'sort-dir' => 'desc',
        ], $options);

        $productId = (int)$productId;
        $shopId = (null === $shopId) ? (int)ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;

        $allowedSorts = [
            'date' => 'c.date',
            'useful' => 'c.useful_counter',
            'rating' => 'c.rating',
        ];
        if (array_key_exists($options['sort'], $allowedSorts)) {
            $sort = $allowedSorts[$options['sort']];
        } else {
            $sort = 'c.date';
        }

        $page = (int)$options;
        if ($page < 1) {
            $page = 1;
        }

        $rows = QuickPdoRowsGenerator::create()
            ->setNbItemsPerPage(20)
            ->setPage($page)
            ->setSortValues([
                $sort => $options['sort-dir'],
            ])
            ->setFields('
c.date,
unix_timestamp(c.date) as time,        
c.rating,  
c.useful_counter,
c.title,
c.comment,
u.pseudo   
    ')
            ->setQuery("select %s 
from ek_product_comment c 
inner join ek_user u on u.id=c.user_id
where 
c.shop_id=$shopId
and c.product_id=$productId
and c.active=1
")
            ->getRows();


        return $rows;


//        return A::cache()->get("Ekom.ProductCommentLayer.$shopId.$productId", function () use ($shopId, $productId) {
//
//
//
//        }, [
//            "ek_product_comment",
//            "ek_user.update",
//            "ek_user.delete",
//        ]);
    }

}