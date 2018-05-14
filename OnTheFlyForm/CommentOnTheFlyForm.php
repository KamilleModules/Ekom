<?php


namespace Module\Ekom\OnTheFlyForm;


use OnTheFlyForm\OnTheFlyForm;

class CommentOnTheFlyForm extends OnTheFlyForm
{
    public function __construct()
    {
        parent::__construct();
        $this->setIds([
            "title",
            "comment",
            "rating",
        ]);
    }
}