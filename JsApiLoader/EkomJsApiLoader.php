<?php

namespace Module\Ekom\JsApiLoader;


use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

class EkomJsApiLoader
{

    private $js;


    public function __construct()
    {
        $this->js = [];
    }

    public function load()
    {
        HtmlPageHelper::js("/modules/Ekom/js/ekomJsApi.js", null, null, false);
        foreach ($this->js as $uri) {
            HtmlPageHelper::js($uri, null, null, false);
        }
    }


    public function addJsResource($uri)
    {
        $this->js[] = $uri;
        return $this;
    }
}