<?php


namespace Module\Ekom\Model\Front\Customer;


use Module\Ekom\HybridList\HybridListFactory;
use Module\Ekom\Utils\E;


class CustomerInvoicesModel
{

    public static function getModel(array $pool, $userId)
    {


        if (false === array_key_exists("sort", $pool)) {
            $pool['sort'] = "date_desc";
        }
        $hybridList = HybridListFactory::getUserInvoicesHybridList($pool, $userId);
        $info = $hybridList->execute();


        $items = $info['items'];


        foreach ($items as $k => $item) {
            $item['uriDownloadPdf'] = E::link("Ekom_pdf_download", ['pdfId' => "invoice"]) . "?invoice_id=" . $item['id'];
            $items[$k] = $item;
        }
        $info['items'] = $items;


        $model['bundle'] = [
            'general' => $info,
            'slice' => $hybridList->getControl('slice')->getModel(),
            'sort' => $hybridList->getControl('sort')->getModel(),
        ];
        return $model;
    }
}