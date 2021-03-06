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
class OrderSortHybridListControl extends BaseSqlSortHybridListControl
{


    protected function getDefaultSort()
    {
        return 'label_asc'; // default sort
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
                $r->addOrderBy("date", "asc");
                break;
            case "date_desc":
                $r->addOrderBy("date", "desc");
                break;
            case "ref_asc":
                $r->addOrderBy("reference", "asc");
                break;
            case "ref_desc":
                $r->addOrderBy("reference", "desc");
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
            'ref_asc' => 'Par référence croissante',
            'ref_desc' => 'Par référence décroissante',
        ];
    }
}