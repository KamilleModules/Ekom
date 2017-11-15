<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


use Bat\UriTool;
use HybridList\HybridListInterface;
use HybridList\RequestGenerator\RequestGeneratorInterface;
use HybridList\RequestShaper\RequestShaper;
use HybridList\SqlRequest\SqlRequestInterface;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\AttributeLayer;
use Module\Ekom\HybridList\HybridListControl\HybridListControl;

class AttributesFilterHybridListControl extends HybridListControl
{
    private $attrNames;
    private $_alreadyReacted;

    public function __construct()
    {
        parent::__construct();
        $this->attrNames = [];
        $this->_alreadyReacted = false;
    }

    public function prepareHybridList(HybridListInterface $list, array $context)
    {
        $this->_alreadyReacted = false;
        $this->attrNames = AttributeLayer::getAttributeNamesByShopId();
        $this->decorateAttribute(
            $list->getRequestGenerator(),
            $context['category_id'],
            $list->getListParameters()
        );
        return $this;
    }




    //--------------------------------------------
    //
    //--------------------------------------------
    private function decorateAttribute(RequestGeneratorInterface $requestGenerator, $categoryId, array $pool)
    {
        $requestGenerator->addRequestShaper(RequestShaper::create()
            ->reactsTo($this->attrNames)
            ->setExecuteCallback(function ($input, SqlRequestInterface $sqlRequest) use ($categoryId, $pool) {

                /**
                 * The callback will be executed for every attribute (that's the way HybridList works).
                 * For attributes, we don't need that, we rather just want one call for all attributes.
                 * _alreadyReacted is created for that purpose.
                 */
                if (false === $this->_alreadyReacted) {

                    $this->_alreadyReacted = true;

                    /**
                     * collect all attributes, we need them to create the control model anyway
                     */
                    $attributes = AttributeLayer::getAvailableAttributeByCategoryId($categoryId);


                    //--------------------------------------------
                    // filtering only attributes that we actually use
                    //--------------------------------------------
                    $attrValue2Id = [];
                    foreach ($attributes as $k => $attr) {
                        if (!array_key_exists($attr['name'], $pool)) {
                            unset($attributes[$k]);
                        } else {
                            $attrValue2Id[$attr['value']] = $attr['value_id'];
                        }
                    }

                    $already = [];
                    $model = []; // preparing model
                    $atLeastOneAttribute = false;
                    $attrApi = EkomApi::inst()->attributeLayer();


                    $c = 0;
                    foreach ($attributes as $attr) {
                        $name = $attr['name'];


                        if (!array_key_exists($name, $model)) {
                            $model[$name] = [
                                'title' => $attr['name_label'],
                                'type' => "items",
                                'items' => [],
                            ];
                        }


                        //--------------------------------------------
                        // PREPARING THE MODEL
                        //--------------------------------------------
                        /**
                         * The params are in the uri.
                         * Example:
                         * https://lee/category/tapis_de_marche?poids[0]=15_kg&poids[1]=4_kg
                         *
                         */
                        $get = $pool;
                        $getArr = (array_key_exists($name, $pool)) ? $pool[$name] : [];

                        $value = $attr['value'];
                        if (
                            (is_array($getArr) && in_array($value, $getArr, true)) ||
                            (!is_array($getArr) && $value === $getArr)
                        ) {
                            /**
                             * If the attribute already exist in the uri, we remove it from the uri
                             * params (toggle like behaviour)
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
                            if (true === is_array($getArr)) {
                                if (false === array_search($value, $getArr)) {
                                    $get[$name][] = $value;
                                }
                            } else {
                                $get[$name] = $value;
                            }
                        }

                        $uri = UriTool::uri(null, $get);
                        $model[$name]['items'][] = [
                            "label" => $attr['value_label'],
                            "value" => $attr['value'],
                            "uri" => $uri,
                            "selected" => $selected,
                        ];


                        //--------------------------------------------
                        // TAKING CARE OF THE QUERY
                        //--------------------------------------------
                        if (!array_key_exists($name, $already)) {
                            $already[$name] = null;

                            if (array_key_exists($name, $pool)) {

                                $usedPool[] = $name;


                                $values = $pool[$name];
                                if (!is_array($values)) {
                                    $values = [$values];
                                }

                                $safeIds = []; // sometimes, the user might try non-existing values
                                foreach ($values as $attrValue) {
                                    if (array_key_exists($attrValue, $attrValue2Id)) {
                                        $safeIds[] = $attrValue2Id[$attrValue];
                                    } else {
                                        // this value is not found: the request should yield no results
                                        // by the way, this is a hand made request (the website won't
                                        // provide erroneous requests like that)
                                        $safeIds[] = -1;
                                    }
                                }


                                $tagName = "attrname" . $c++;

                                if ($safeIds) {

                                    $atLeastOneAttribute = true;
                                    $sqlRequest->addWhere("
and a.name = :$tagName
and v.id in (" . implode(', ', $safeIds) . ")                          
                        ");
                                    $sqlRequest->addMarker($tagName, $name);
                                }


                            }
                        }
                    }


                    if (true === $atLeastOneAttribute) { // note: maybe this could/should be unconditional?
                        $sqlRequest->addJoin("
inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id                            
");
                    }


                    $this->model = $model;
                }
            })
        );
    }


}