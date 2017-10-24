<?php


namespace Module\Ekom\QueryFilterBox\QueryFilterBox;


use Bat\UriTool;
use Module\Ekom\Api\Layer\DiscountLayer;
use Module\Ekom\QueryFilterBox\CategoryAwareQueryFilterBoxInterface;
use Module\Ekom\Utils\E;
use QueryFilterBox\Collectable\CollectableInterface;
use QueryFilterBox\Query\Query;
use QueryFilterBox\QueryFilterBox\QueryFilterBox;


class DiscountQueryFilterBox extends QueryFilterBox implements CollectableInterface, CategoryAwareQueryFilterBoxInterface
{

    private $categoryId;
    private $_discounts;


    public function __construct()
    {
        parent::__construct();
        $this->_discounts = [];
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public static function create()
    {
        return new static();
    }


    //--------------------------------------------
    // COLLECTABLE
    //--------------------------------------------
    public function collect($param, $value)
    {
        if ('discounts' === $param) {
            return [
                'keyLabel' => 'Réduction',
                'valueLabel' => "Réduction -" . $value . "%",
            ];
        }
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    protected function doDecorateQuery(Query $query, array $pool, array &$usedPool)
    {

        if (array_key_exists("discounts", $pool)) {
            $usedPool[] = "discounts";

            $discounts = $pool['discounts'];
            $discounts = array_map(function ($v) {
                $v = (int)$v;
                return '..' . $v;
            }, $discounts);
            $sBadges = implode('|', $discounts);
            $query->addWhere("
shp._discount_badge REGEXP '$sBadges'
        ");
        }

    }

    public function prepare()
    {

        $o = new DiscountLayer();
        $badges = $o->getDiscountBadges([
            'categoryId' => $this->categoryId,
            'procedureType' => 'percent',
        ]);


        $poolBadges = array_key_exists('discounts', $this->pool) ? $this->pool['discounts'] : [];

        $badgesModel = [];
        $already = [];
        foreach ($badges as $badge) {
            $badgeInt = substr($badge, 2);

            $bm = $poolBadges;
            $selected = false;
            if (in_array($badgeInt, $poolBadges)) {
                $selected = true;
                unset($bm[array_search($badgeInt, $poolBadges)]);
            } else {
                $bm[] = $badgeInt;
            }
            $uri = UriTool::uri(null, [
                'discounts' => $bm,
            ]);


            if (!in_array($badgeInt, $already)) {
                $already[] = $badgeInt;
                $badgesModel[] = [
                    'value' => $badgeInt,
                    'label' => $this->getBadgeLabel($badge),
                    'selected' => $selected,
                    'uri' => $uri,
                ];
            }
        }
        $this->model = [
            'badges' => $badgesModel,
        ];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getBadgeLabel($name)
    {
        $n = substr($name, 2);
        return "-" . $n . "%";
    }

}