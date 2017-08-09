<?php


namespace Module\Ekom\ListParams\ListBundleFactory;


use ListParams\Controller\PaginationFrame;
use ListParams\Controller\SortFrame;
use ListParams\ListBundle\ListBundle;
use ListParams\ListBundle\ListBundleInterface;
use ListParams\ListBundleFactory\ListBundleFactoryInterface;
use ListParams\ListParams;


/**
 * default ekom list bundle factory for ekom lists
 */
class EkomListBundleFactory implements ListBundleFactoryInterface
{

    public function getListBundle($identifier)
    {
        switch ($identifier) {
            case "customer.account.orders":

                $params = ListParams::create()->infuse();
                $model = new Model();
                $items = $model->getOrderItems($params);
                $pagination = PaginationFrame::createByParams($params);
                $sortLabels = [];
                $fields = $params->getAllowedSortFields();
                foreach ($fields as $field) {
                    $sortLabels[$field] = __($field);
                }
                $sort = SortFrame::createByLabels($sortLabels, $params);


                return ListBundle::create()
                    ->setListParams($params)
                    ->setItems($items)
                    ->setPagination($pagination)
                    ->setSort($sort);

                break;
        }
        throw new \Exception("unknown ListBundle: $identifier");
    }

}