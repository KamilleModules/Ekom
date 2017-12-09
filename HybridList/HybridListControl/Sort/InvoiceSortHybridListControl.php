<?php


namespace Module\Ekom\HybridList\HybridListControl\Sort;


use HybridList\HybridListControl\HybridListControl;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use HybridList\RequestShaper\RequestShaper;
use HybridList\SqlRequest\SqlRequestInterface;


/**
 *
 * The returned model is the sort component of a listBundle model
 * https://github.com/lingtalfi/Models/tree/master/ListBundle
 *
 */
class InvoiceSortHybridListControl extends BaseSqlSortHybridListControl
{
    protected function getDefaultSort()
    {
        return "amount_asc";
    }


    protected function decorateSqlRequestByInput($input, SqlRequestInterface $r)
    {
        switch ($input) {
            case "amount_asc":
                $r->addOrderBy("amount", "asc");
                break;
            case "amount_desc":
                $r->addOrderBy("amount", "desc");
                break;
            case "date_asc":
                $r->addOrderBy("invoice_date", "asc");
                break;
            case "date_desc":
                $r->addOrderBy("invoice_date", "desc");
                break;
            case "number_asc":
                $r->addOrderBy("invoice_number", "asc");
                break;
            case "number_desc":
                $r->addOrderBy("invoice_number", "desc");
                break;
            default:
                break;
        }
    }


    protected function getSort2Label()
    {
        return [
            'amount_asc' => 'Par montant croissant',
            'amount_desc' => 'Par montant décroissant',
            'date_asc' => 'Par date croissante',
            'date_desc' => 'Par date décroissante',
            'number_asc' => 'Par numéro de facture croissant',
            'number_desc' => 'Par numéro de facture décroissant',
        ];
    }
}