<?php


namespace Module\Ekom\ListParams\ListBundleFactory;


use ListParams\Controller\InfoFrame;
use ListParams\Controller\PaginationFrame;
use ListParams\Controller\SortFrame;
use ListParams\ListBundle\ListBundle;
use ListParams\ListBundle\ListBundleInterface;
use ListParams\ListBundleFactory\ListBundleFactoryInterface;
use ListParams\ListParams;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;


/**
 * default ekom list bundle factory for ekom lists
 */
class EkomListBundleFactory implements ListBundleFactoryInterface
{

    private $listBundleCallables;


    public function __construct()
    {
        $this->listBundleCallables = [];
    }


    public function getListBundle($identifier)
    {


        foreach ($this->listBundleCallables as $cb) {
            $listBundle = call_user_func($cb, $identifier);
            if ($listBundle instanceof ListBundleInterface) {
                return $listBundle;
            }
        }


        $items = null;
        $params = null;
        $pagination = null;
        $sort = null;

        switch ($identifier) {
            case "customer.account.orders":

                $params = ListParams::create()->infuse();

                $userId = E::getUserId();
                $items = EkomApi::inst()->orderLayer()->getUserAccountOrderItems($userId, $params);

                $pagination = PaginationFrame::createByParams($params);
                $sortLabels = [];
                $fields = $params->getAllowedSortFields();
                foreach ($fields as $field) {
                    $sortLabels[$field] = __($field);
                }
                $sort = SortFrame::createByLabels($sortLabels, $params);


                break;
        }
        if (null !== $items) {
            $list = ListBundle::create()->setItems($items);

            if (null !== $params) {
                $list->setListParams($params);
            }

            if (null !== $pagination) {
                $list->setPagination($pagination);
            }

            if (null !== $sort) {
                $list->setSort($sort);
            }

            $list->setInfo(InfoFrame::create($params));

            return $list;
        }
        throw new \Exception("unknown ListBundle: $identifier");
    }


    public function registerListBundleCallable(callable $returnBundle)
    {
        $this->listBundleCallables[] = $returnBundle;
        return $this;
    }
}