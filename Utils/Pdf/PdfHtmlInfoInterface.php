<?php


namespace Module\Ekom\Utils\Pdf;


use Module\Ekom\Exception\EkomUserMessageException;

interface PdfHtmlInfoInterface
{
    /**
     * @param callable $htmlCallback
     *                  string:html  htmlCallback ( string:id, array:params=[] )
     * @throws EkomUserMessageException to communicate info with the customer
     * @throws \Exception if something else goes wrong
     *
     * @return mixed
     */
    public function prepareByHtmlCallback(callable $htmlCallback);

    public function getHtml();
}