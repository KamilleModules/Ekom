<?php


namespace Module\Ekom\Utils\Pdf;


class PdfHtmlInfo implements PdfHtmlInfoInterface
{

    private $id;
    private $params;
    private $html;
    private $isPrepared;


    public function __construct()
    {
        $this->html = "";
        $this->isPrepared = false;
    }

    public static function create()
    {
        return new static();
    }

    public function init($id, array $params = [])
    {
        $this->id = $id;
        $this->params = $params;
        return $this;
    }

    public function prepareByHtmlCallback(callable $htmlCallback)
    {
        if (false === $this->isPrepared) {
            $this->isPrepared = true;
            $this->html = call_user_func($htmlCallback, $this->id, $this->params);
        }
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getHtml()
    {
        return $this->html;
    }
}