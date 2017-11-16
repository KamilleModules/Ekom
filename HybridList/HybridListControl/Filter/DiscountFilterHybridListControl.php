<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


use Bat\UriTool;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;
use HybridList\RequestGenerator\RequestGeneratorInterface;
use HybridList\RequestShaper\RequestShaper;
use HybridList\SqlRequest\SqlRequestInterface;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Api\Layer\AttributeLayer;
use Module\Ekom\HybridList\HiddenFormFieldsHelper;
use Module\Ekom\HybridList\HybridListControl\HybridListControl;
use Module\Ekom\Utils\E;


/**
 * This discount filter only filters discounts of type percent
 */
class DiscountFilterHybridListControl extends HybridListControl implements SummaryFilterAwareInterface
{

    /**
     * @var array percent discounts found in the current set of boxes
     */
    private $availableDiscounts;
    private $pool;

    public function __construct()
    {
        parent::__construct();
        $this->availableDiscounts = [];
        $this->pool = [];
    }


    public function prepareHybridList(HybridListInterface $list, array $context)
    {
        $this->pool = $context['pool'];
        //--------------------------------------------
        // shape request
        //--------------------------------------------
        $list
            ->addListShaper(ListShaper::create()
                ->reactsTo("discounts")
                ->setPrepareCallback(function(array $originalBoxes){
                    foreach ($originalBoxes as $k => $box) {
                        //--------------------------------------------
                        // MODEL PART
                        //--------------------------------------------
                        if (is_array($box['discount']) && 'percent' === $box['discount']['type']) {
                            $this->availableDiscounts[] = $box['discount']['operand'];
                        }
                    }
                })
                ->setExecuteCallback(function ($input, array &$boxes, array &$info = [], $originalBoxes) use ($context) {
                    if (!is_array($input)) {
                        $input = [$input];
                    }
                    $inputBadges = [];
                    foreach ($input as $operand) {
                        $inputBadges[] = "pp" . $operand;
                        $inputBadges[] = "pc" . $operand;
                        $inputBadges[] = "pt" . $operand;
                    }
                    $inputBadges = array_unique($inputBadges);


                    foreach ($boxes as $k => $box) {
                        //--------------------------------------------
                        // FILTERING PART
                        //--------------------------------------------
                        if (!in_array($box['discountBadge'], $inputBadges)) {
                            unset($boxes[$k]);
                            $info['totalNumberOfItems'] = $info['totalNumberOfItems'] - 1;
                        }
                    }
                })
            );


        return $this;

    }


    public function getSummaryFilterItem($param, $value)
    {
        if ('discounts' === $param) {
            return "Réduction -" . $value . "%";
        }
    }


    public function getModel()
    {
        if (empty($this->model)) {


            $poolBadges = array_key_exists('discounts', $this->pool) ? $this->pool['discounts'] : [];

            $badgesModel = [];
            $already = [];
            foreach ($this->availableDiscounts as $badgeInt) {

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
                        'label' => "-" . $badgeInt . "%",
                        'selected' => $selected,
                        'uri' => $uri,
                    ];
                }
            }
            $this->model = [
                'badges' => $badgesModel,
            ];
        }
        return $this->model;
    }

}