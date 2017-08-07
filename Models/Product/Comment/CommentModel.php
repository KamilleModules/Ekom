<?php


namespace Module\Ekom\Models\Product\Comment;


use QuickPdo\QuickPdo;

class CommentModel
{


    private $id;
    private $shop_id;
    private $product_id;
    private $user_id;
    private $date;
    private $rating;
    private $useful_counter;
    private $title;
    private $comment;
    private $active;


    public function __construct()
    {
        $this->id = 0;
        $this->shop_id = 0;
        $this->product_id = 0;
        $this->user_id = 0;
        $this->date = date("Y-m-d H:i:s");
        $this->rating = 0;
        $this->useful_counter = 0;
        $this->title = "";
        $this->comment = "";
        $this->active = 0;
        $this->id = 0;
        $this->shop_id = 0;
        $this->product_id = 0;
        $this->user_id = 0;
    }


    public static function createById($id)
    {
        $id = (int)$id;
        $o = new static();
        if (false !== ($row = QuickPdo::fetch("select * from ek_product_comment where id=$id"))) {
            return self::createByRow($row);
        }
        return $o;
    }

    public static function createByRow(array $row)
    {
        $o = new static();
        $o->id = $row['id'];
        $o->shop_id = $row['shop_id'];
        $o->product_id = $row['product_id'];
        $o->user_id = $row['user_id'];
        $o->date = $row['date'];
        $o->rating = $row['rating'];
        $o->useful_counter = $row['useful_counter'];
        $o->title = $row['title'];
        $o->comment = $row['comment'];
        $o->active = $row['active'];
        return $o;
    }











    //--------------------------------------------
    // SETTERS/GETTERS
    //--------------------------------------------
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shop_id;
    }

    public function setShopId($shop_id)
    {
        $this->shop_id = $shop_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsefulCounter()
    {
        return $this->useful_counter;
    }

    public function setUsefulCounter($useful_counter)
    {
        $this->useful_counter = $useful_counter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }


}