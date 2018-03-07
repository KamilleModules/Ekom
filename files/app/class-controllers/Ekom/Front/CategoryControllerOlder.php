<?php


namespace Controller\Ekom\Front;


use Bat\UriTool;
use Controller\Ekom\EkomFrontController;
use Core\Services\X;
use Kamille\Ling\Z;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Services\XConfig;
use Kamille\Services\XLog;
use Kamille\Utils\Laws\Config\LawsConfig;
use Kamille\Utils\Laws\DynamicWidgetBinder\DynamicWidgetBinder;
use ListModifier\Circle\ListModifierCircle;
use ListModifier\RequestModifier\RequestModifierInterface;
use ListModifier\Util\ListModifierUtil;
use Models\ListSortBar\Lee\LeeListSortBarModel;
use Models\Pagination\Lee\LeePaginationModel;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

class CategoryControllerOlder extends EkomFrontController
{


    /**
     *
     * Technical details
     * =====================
     *
     * The intent is to render a list of products,
     * and a sidebar on the left (or right)
     * with widgets to control the shape of the list.
     * Those widgets on the sidebar are list filters.
     *
     * To display them, one thing we would like to provide is the number
     * of products group by attribute values.
     *
     *
     * In order to get this information, we first generate the list,
     * including the list modifiers,
     * and then from there we manually distribute the number of products by attribute value.
     *
     *
     *
     */
    public function render()
    {

        $slug = Z::getUrlParam("slug");
        $isB2b = E::isB2b();


       if (null !== $slug) {

            if (false !== ($catId = EkomApi::inst()->categoryLayer()->getIdBySlug($slug))) {


                //--------------------------------------------
                // SOME CONFIG
                //--------------------------------------------
                $namePage = "page";
                $nipp = XConfig::get("Ekom.nipp.category");


                //--------------------------------------------
                //
                //--------------------------------------------
                $nbTotalItems = 0;
                /**
                 * @var $circle ListModifierCircle
                 */
                $circle = X::get("Core_ListModifierCircle");
                $circle->clean();
                $circle->setListModifier(['sort', 'sort-dir'], function (RequestModifierInterface $modifier) use ($isB2b) {
                    if (
                        array_key_exists('sort', $_GET) &&
                        array_key_exists('sort-dir', $_GET)
                    ) {
                        $sort = $_GET['sort'];
                        switch ($sort) {
                            case 'label':
                            case 'name':
                                $sort = "label";
                                break;
                            case 'price':
                                if ($isB2b) {
                                    $sort = "shp._sale_price_without_tax";
                                } else {
                                    $sort = "shp._sale_price_with_tax";
                                }
                                break;
                            default:
                                $sort = "p.id";
                                break;
                        }
                        $modifier->addSortItem($sort, $_GET['sort-dir']);


                    }
                });


                $currentPage = (array_key_exists($namePage, $_GET)) ? $_GET[$namePage] : 1;
                $circle->setListModifier($namePage, function (RequestModifierInterface $modifier) use ($nipp, $currentPage) {
                    $offset = ($currentPage - 1) * $nipp;
                    $modifier->setLimit($offset, $nipp);
                });


//                $attributes = EkomApi::inst()->attributeLayer()->getAvailableAttributeByCategorySlug($slug);
//                az($attributes);

//                $attrName2Label = [];
//                foreach ($attributes as $name => $attrs) {
//                    $attrName2Label[$name] = $attrs['label'];
//                }


                $summaryItems = [];


                //--------------------------------------------
                // CREATING THE CIRCLE AND EXECUTING THE REQUEST
                //--------------------------------------------
                /**
                 * Note: call getFilterBoxesByCategoryId BEFORE getProductCardsByCategory,
                 * because getProductCardsByCategory use the list modifiers set by getFilterBoxesByCategoryId.
                 */
                $filterBoxes = $this->getFilterBoxesByCategoryId($circle, $catId, $isB2b, $summaryItems);
                $cards = EkomApi::inst()->productCardLayer()->getProductCardsByCategory($catId, $isB2b, $circle, null, null, $nbTotalItems);

//                $filterBoxes = $this->getFilterBoxesByCards($cards, $summaryItems);
                $get = $_GET;
                $possibleNames = $circle->getListModifierNames();
                foreach ($possibleNames as $name) {
                    unset($get[$name]);
                }

                $clearAllUri = UriTool::uri(null, $get);


                //--------------------------------------------
                // USING THE CIRCLE TO PROVIDE INFO TO THE TEMPLATES
                //--------------------------------------------
                $formTrail = ListModifierUtil::toFormFields($circle, ['sort', 'sort-dir']);
                $circleValues = ListModifierUtil::getCircleValues($circle);

//                az($cards);


                //--------------------------------------------
                // SUMMARY
                //--------------------------------------------
//                $possibleListFilterNames = [
//                    "price",
//                ];
//                $possibleListFilterNames = array_merge($possibleListFilterNames, array_keys($attributes));
////                az($possibleListFilterNames);
//                $summaryItems = [];
//                $uriClearParams = [];
//                foreach ($_GET as $k => $v) {
//                    if (in_array($k, $possibleListFilterNames, true)) {
//
//                        $label = (array_key_exists($k, $attrName2Label)) ? $attrName2Label[$k] : $v;
//
//                        $summaryItems[$label] = [
//                            'value' => $v,
//                            'label' => $v,
//                        ];
//                    } else {
//                        $uriClearParams[$k] = $v;
//                    }
//                }


//                $uriClearAll = UriTool::uri(null, $uriClearParams, true);

                /**
                 * @var $binder DynamicWidgetBinder
                 */
//                $binder = X::get("Ekom_dynamicWidgetBinder"); // trying to deprecate it because too complex


                $listSortBar = LeeListSortBarModel::createByName2Values([
                    'name' => "Name",
                    'price' => "Price",
                ], $circleValues)
                    ->setFormTrail($formTrail)
                    ->setNbItems(count($cards));


                // your app


                // creating the model
                $pagination = LeePaginationModel::createByInfo($nbTotalItems, $nipp, $namePage);


                if (false === 'demo') { // creating the model alternative
                    $uri = UriTool::uri(null, [$namePage => '%s'], false);
                    $linkFormatter = function ($n) use ($uri) {
                        return sprintf($uri, $n);
                    };
                    $pagination = LeePaginationModel::createByInfo($nbTotalItems, $nipp, $namePage, $linkFormatter);
                }


                $config = [
                    "maincontent.productList" => [
                        'conf' => [
                            'cards' => $cards,
                            'nbCards' => count($cards),
                            'formTrail' => $formTrail,
                            'circleValues' => $circleValues,
                            'listSortBar' => $listSortBar->getArray(),
                            'pagination' => $pagination->getArray(),
                        ],
                    ],
                    "sidebar.summaryFilters" => [
                        'conf' => [
                            "items" => $summaryItems,
                            "uriClearAll" => $clearAllUri,
//                            "items" => $summaryItems,
//                            "uriClearAll" => $uriClearAll,
                        ],
                    ],
                    "sidebar.attributeFilters" => [
                        'conf' => [
                            'filterBoxes' => $filterBoxes,
                        ],
                    ],
//                    "sidebar.commonFilters" => [
//                        'conf' => [
//
//                        ],
//                    ],
                ];
//                $binder->notify("productListModifiers", $attributes, $config);


                return $this->renderByViewId("Ekom/category", LawsConfig::create()->replace([
                    "widgets" => $config,
                ]));
            }


        } else {
            // tmp workaround
            $category = Z::getUrlParam("category", "equipement");
            return $this->renderByViewId("Ekom/category-$category");
        }
//        return $this->renderByViewId("Ekom/category");
    }




    private function getFilterBoxesByCategoryId(ListModifierCircle $circle, $categoryId, $isB2b, array &$summaryItems)
    {


        $ret = [];
        $isArrayByName = [
            '*' => true,
        ];


        //--------------------------------------------
        // ADDING ALL ATTRIBUTES
        //--------------------------------------------
        $attrApi = EkomApi::inst()->attributeLayer();
        $attributes = EkomApi::inst()->attributeLayer()->getAvailableAttributeByCategoryId($categoryId);
        foreach ($attributes as $attr) {
            $name = $attr['name'];
            if (!array_key_exists($name, $ret)) {
                $ret[$name] = [
                    'title' => $attr['name_label'],
                    'type' => "items",
                    'items' => [],
                ];

                $circle->setListModifier($name, function (RequestModifierInterface $modifier) use ($attrApi, $name) {
                    if (array_key_exists($name, $_GET)) {
                        $values = $_GET[$name];
                        if (!is_array($values)) {
                            $values = [$values];
                        }
                        $safeIds = [];
                        foreach ($values as $v) {
                            if (false !== ($valueId = $attrApi->getAttrValueBySlug($v))) {
                                $safeIds[] = $valueId;
                            }
                        }

                        $modifier->addSearchItem($name, $safeIds, "in");
                    }
                });
            }


            $isArray = (array_key_exists($name, $isArrayByName)) ? $isArrayByName[$name] : $isArrayByName['*'];
            $get = $_GET;
            $getArr = (array_key_exists($name, $_GET)) ? $_GET[$name] : [];

            $value = $attr['value'];
            if (
                (is_array($getArr) && in_array($value, $getArr, true)) ||
                (!is_array($getArr) && $value === $getArr)
            ) {
                /**
                 * If the attribute already exist in the uri,
                 * the value disappears from the list filter widget and goes to the summary
                 * widget (as a user current filter).
                 */
                $selected = true;
                if (is_array($getArr)) {
                    if (false !== ($index = array_search($value, $getArr))) {
                        unset($get[$name][$index]);
                        if (0 === count($get[$name])) {
                            unset($get[$name]);
                        }
                    }
                } else {
                    unset($get[$name]);
                }


            } else {
                $selected = false;
                if (true === $isArray) {
                    if (false === array_search($value, $getArr)) {
                        $get[$name][] = $value;
                    }
                } else {
                    $get[$name] = $value;
                }
            }

            $uri = UriTool::uri(null, $get);
            if (true === $selected) {
                $summaryItems[$name][] = [
                    'label' => $attr['value_label'],
                    'uri' => $uri,
                ];
            }

            $ret[$name]['items'][] = [
                "label" => $attr['value_label'],
                "uri" => $uri,
                "selected" => $selected,
            ];
        }


        //--------------------------------------------
        // PRICE
        //--------------------------------------------
        $minMax = EkomApi::inst()->productLayer()->getMinMaxSalePrice($categoryId);
        $circle->setListModifier('price', function (RequestModifierInterface $modifier) {
            if (array_key_exists("price", $_GET)) {
                $p = explode('-', $_GET['price'], 2);
                if (2 === count($p)) {
                    $min = (float)$p[0];
                    $max = (float)$p[1];
                    $modifier->addSearchItem("price", $min, "between", $max);
                }
            }
        });


        if (true === $isB2b) {
            $min = (float)$minMax['minSalePriceWithoutTax'];
            $max = (float)$minMax['maxSalePriceWithoutTax'];
        } else {
            $min = (float)$minMax['minSalePriceWithTax'];
            $max = (float)$minMax['maxSalePriceWithTax'];
        }
        if ($min !== $max) {


            $currentMin = $min;
            $currentMax = $max;
            if (array_key_exists('price', $_GET)) {
                $p = explode('-', $_GET['price'], 2);
                if (2 === count($p)) {
                    $currentMin = $p[0];
                    $currentMax = $p[1];
                    $get = $_GET;
                    unset($get['price']);
                    $uri = UriTool::uri(null, $get);
                    $summaryItems["price"][] = [
                        'label' => E::price($currentMin) . " - " . E::price($currentMax),
                        'uri' => $uri,
                    ];
                }


            }
            $formTrail = ListModifierUtil::toFormFields($circle, ['price']);

            $ret['price'] = [
                "formTrail" => $formTrail,
                "title" => "Price",
                "type" => "minMax",
                "minValue" => $this->formatPrice($min),
                "maxValue" => $this->formatPrice($max),
                "currentMin" => $this->formatPrice($currentMin),
                "currentMax" => $this->formatPrice($currentMax),
                "moneyFormatArgs" => E::conf("moneyFormatArgs"),
            ];
        }
        return $ret;
    }

    private function formatPrice($n)
    {
        return sprintf('%.2f', $n);
    }

}


