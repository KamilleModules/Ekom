<?php


namespace Module\Ekom\OnTheFlyForm;


use Module\Ekom\Api\EkomApi;
use OnTheFlyForm\OnTheFlyForm;

class CommentOnTheFlyForm extends OnTheFlyForm
{


    protected function getBaseModel()
    {
        return [

            "nameTitle" => "title",
            "nameComment" => "comment",
            "nameRating" => "rating",


            "valueTitle" => "",
            "valueComment" => "",
            "valueRating" => "",

            //
            "errorComment" => "",
            "errorRating" => "",
        ];
    }

    protected function getField2Validators()
    {
        return [
            'comment' => ['required'],
            'rating' => ['required'],
        ];
    }

}