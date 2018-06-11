<?php


namespace Module\Ekom\SqlQueryWrapper\Plugins;


use Bat\UriTool;
use Core\Services\A;
use Kamille\Services\XConfig;
use Module\Application\Helper\ApplicationHashHelper;
use QuickPdo\QuickPdo;
use SqlQuery\SqlQueryInterface;
use SqlQueryWrapper\Plugins\SqlQueryWrapperBasePlugin;

class EkomSqlQueryWrapperDiscountFilterPlugin extends SqlQueryWrapperBasePlugin implements EkomSummaryFilterHelperInterface
{

    protected $discountKey;
    protected $discounts;


    public function __construct()
    {
        parent::__construct();
        $this->discountKey = "discount";
        $this->discounts = [];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function getSummaryItemLabel(string $param, $value)
    {
        if ($this->discountKey === $param) {
            return "Réduction -" . $value . "%";
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function onQueryReady(SqlQueryInterface $sqlQuery)
    {

        //--------------------------------------------
        // YIELDING THE DISCOUNT INFO
        //--------------------------------------------
        $sqlQueryString = $sqlQuery->getSqlQuery();
        $markers = $sqlQuery->getMarkers();
        $q = "
select
discount_id,
discount_label,
discount_type,
discount_value,
count(discount_id) as count

from ($sqlQueryString) as zz 
where discount_type='p'
group by discount_value
                ";

        $hash = ApplicationHashHelper::getHashByQuery($q, $markers);
        $this->discounts = A::cache()->getDaily("Ekom.EkomSqlQueryWrapperDiscountFilterPlugin.$hash", function () use ($q, $markers) {
            return QuickPdo::fetchAll($q, $markers);
        });
    }


    public function prepareQuery(SqlQueryInterface $sqlQuery)
    {
        if (array_key_exists($this->discountKey, $this->context)) {

            $discount = $this->context[$this->discountKey] ?? null;
            if (!is_array($discount)) {
                $discount = [$discount];
            }
            $sDiscounts = implode(', ', $discount);
            $sqlQuery->addHaving("(discount_type='p' and discount_value in ($sDiscounts))", "group1");
        }
    }

    public function prepareModel(int $nbItems, array $rows)
    {
        $poolBadges = $this->context[$this->discountKey] ?? [];

        $badgesModel = [];
        foreach ($this->discounts as $discount) {

            $badgeInt = $discount['discount_value'];

            $bm = $poolBadges;
            $selected = false;
            if (in_array($badgeInt, $poolBadges)) {
                $selected = true;
                unset($bm[array_search($badgeInt, $poolBadges)]);
            } else {
                $bm[] = $badgeInt;
            }
            $uri = UriTool::uri(null, [
                $this->discountKey => $bm,
            ], false);


            $badgesModel[] = [
                'value' => $badgeInt,
                'label' => "-" . $badgeInt . "%",
                'selected' => $selected,
                'uri' => $uri,
                'count' => $discount['count'],
            ];

        }
        $this->model = [
            'badges' => $badgesModel,
        ];
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function setDiscountKey(string $discountKey)
    {
        $this->discountKey = $discountKey;
        return $this;
    }


}

