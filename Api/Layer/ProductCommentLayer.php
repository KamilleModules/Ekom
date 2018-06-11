<?php


namespace Module\Ekom\Api\Layer;


use Bat\UriTool;
use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Module\Application\RowsGenerator\ApplicationRowsGenerator;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Object\ProductComment;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;
use RowsGenerator\QuickPdoRowsGenerator;

class ProductCommentLayer
{

    public static function updateActiveById(int $commentId, $isActive)
    {
        $isActive = (int)$isActive;
        QuickPdo::update("ek_product_comment", [
            'active' => $isActive,
        ], [
            ['id', "=", $commentId],
        ]);
    }

    public static function getCommentsByUserId($userId, $fetchProduct = false, int $limit = null)
    {
        $userId = (int)$userId;
        $q = "
select  

id,
product_id,
`date`,
rating,
useful_counter,
title,
comment,
active

from ek_product_comment where user_id=$userId
        ";

        if (null !== $limit) {
            $q .= " limit 0, $limit";
        }

        $rows = QuickPdo::fetchAll($q);


        if (true === $fetchProduct) {
            foreach ($rows as $k => $row) {
                $box = ProductBoxLayer::getProductBoxByProductId($row['product_id']);
                $box['comment_date'] = $row['date'];
                $box['comment_rating'] = $row['rating'];
                $box['comment_useful_counter'] = $row['useful_counter'];
                $box['comment_title'] = $row['title'];
                $box['comment_comment'] = $row['comment'];
                $box['comment_active'] = $row['active'];
                $box['comment_id'] = $row['id'];
                $rows[$k] = $box;
            }
        }

        return $rows;
    }


    /**
     * Use this method when the user tries to insert a comment for a product on the website.
     */
    public function insertComment($productId, array $data, $userId = null)
    {
        if (null === $userId) {
            $userId = E::getUserId();
        }
        $title = (array_key_exists('title', $data)) ? $data['comment'] : '';

        $commentNeedValidation = E::conf("commentNeedValidation");
        $commentModeratorEmail = E::conf("commentModeratorEmail");
        $active = 1;
        if (true === $commentNeedValidation) {
            $active = 0;
        }


        // rating on 100
        $rating = (int)$data['rating'];
        if ($rating < 0) {
            $rating = 0;
        }
        if ($rating > 100) {
            $rating = 100;
        }


        $date = date('Y-m-d H:i:s');
        $commentId = ProductComment::getInst()->create([
            'product_id' => $productId,
            'user_id' => $userId,
            'date' => $date,
            'rating' => $rating,
            'useful_counter' => 0,
            'title' => $title,
            'comment' => $data['comment'],
            'active' => $active,
        ]);

        $userInfo = EkomApi::inst()->userLayer()->getUserInfo($userId);
        $userEmail = $userInfo['email'];

//        $info = ProductLayer::getProductLabelAndRefByProductId($productId);
//        $boxModel = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($productId);
        $boxModel = ProductBoxLayer::getProductBoxByProductId($productId);


        $link = $boxModel['product_uri_with_details'];
        $link = UriTool::uri($link, [], true, true);


        /**
         * @todo-ling: I removed temporarily the email for the demo because the email template wasn't ready.
         * We need to put them back...
         */
        if (true === $commentNeedValidation) {


            // send email to moderator
            if (false === E::sendMail("Ekom/fra/front/comment.new", $commentModeratorEmail, [
//                    "subject" => "{siteName}: a comment awaits your moderation",
                    "subject" => "Un commentaire est en attente de modération",
                    'productLabel' => $boxModel['label'],
                    'productRef' => $boxModel['reference'],
                    'productUri' => $link,
                    'title' => $title,
                    'comment' => $data['comment'],
                    'date' => $date,
                ])
            ) {
                XLog::error("[Ekom module] - ProductCommentLayer: couldn't send commentAwaitsModeration email to $commentModeratorEmail");
            }


            // send email to user
            if (false === E::sendMail("Ekom/fra/front/comment.your_comment_is_pending", $userEmail, [
                    "subject" => "Votre commentaire est en attente de validation",
//                    "subject" => "{siteName}: your comment awaits moderation",
                ])
            ) {
                XLog::error("[Ekom module] - ProductCommentLayer: couldn't send YourCommentAwaitsModeration email to $userEmail");
            }

        } else {
            // send email to user


            if (false === E::sendMail("Ekom/fra/front/comment.your_comment_has_been_approved", $userEmail, [
                    "subject" => "Votre commentaire a été approuvé",
//                    "subject" => "{siteName}: votre commentaire a été approuvé",
                    'productLabel' => $boxModel['label'],
                    'productRef' => $boxModel['reference'],
                    'productUri' => $link,
                ])
            ) {
                XLog::error("[Ekom module] - ProductCommentLayer: couldn't send yourCommentHasBeenApproved email to $userEmail");
            }
        }

        return $commentId;
    }


    public function getCommentsByProductId($productId, array $options = [])
    {

        $options = array_replace([
            'page' => 1,
            'sort' => 'date',
            'sort-dir' => 'desc',
        ], $options);

        $productId = (int)$productId;

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

        $rows = ApplicationRowsGenerator::create()
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
c.product_id=$productId
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