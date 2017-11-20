<?php


namespace Module\Ekom\HybridList\HybridListControl\Filter;


use Bat\UriTool;
use HybridList\HybridListControl\HybridListControl;
use HybridList\HybridListInterface;
use HybridList\ListShaper\ListShaper;


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
                ->setPrepareCallback(function (array $originalBoxes) {
                    foreach ($originalBoxes as $k => $box) {
                        //--------------------------------------------
                        // MODEL PART
                        //--------------------------------------------
                        if (array_key_exists('discount', $box)) {
                            if (is_array($box['discount']) && 'percent' === $box['discount']['type']) {
                                $this->availableDiscounts[] = $box['discount']['operand'];
                            }

                        } else {
                            az(__FILE__, $box);
                            /**
                             * array(4) {
                             * ["errorCode"] => string(9) "exception"
                             * ["errorMessage"] => string(87) "SQLSTATE[08S01]: Communication link failure: 1053 Server shutdown in progress(384, 709)"
                             * ["errorTitle"] => string(18) "Exception occurred"
                             * ["errorTrace"] => string(3274) "#0 /Volumes/Macintosh HD 2/web/php/projects/universe/planets/QuickPdo/QuickPdo.php(321): PDOStatement->execute(Array)
                             * #1 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/Api/Layer/CommentLayer.php(23): QuickPdo\QuickPdo::fetchAll('select id from ...', Array, 7)
                             * #2 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/Api/Entity/ProductBoxEntity.php(589): Module\Ekom\Api\Layer\CommentLayer->getRatingInfo(384)
                             * #3 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/Api/Entity/ProductBoxEntity.php(250): Module\Ekom\Api\Entity\ProductBoxEntity->getPrimitiveModel(Array, false)
                             * #4 [internal function]: Module\Ekom\Api\Entity\ProductBoxEntity->Module\Ekom\Api\Entity\{closure}()
                             * #5 /Volumes/Macintosh HD 2/web/php/projects/universe/planets/TabathaCache/Cache/TabathaCache.php(72): call_user_func(Object(Closure))
                             * #6 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/Api/Entity/ProductBoxEntity.php(315): TabathaCache\Cache\TabathaCache->get('ekom-pbox-d991f...', Object(Closure), Array)
                             * #7 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/Api/Layer/ProductBoxLayer.php(51): Module\Ekom\Api\Entity\ProductBoxEntity->getModel()
                             * #8 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/HybridList/CategoryHybridList.php(19): Module\Ekom\Api\Layer\ProductBoxLayer::getProductBoxByCardId('384', '709')
                             * #9 /Volumes/Macintosh HD 2/web/php/projects/universe/planets/HybridList/HybridList.php(124): Module\Ekom\HybridList\CategoryHybridList->preparePhpItems(Array)
                             * #10 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Ekom/Model/Front/DynamicProductListModel.php(54): HybridList\HybridList->execute()
                             * #11 /Volumes/Macintosh HD 2/web/php/projects/leaderfit/leaderfit/class-controllers/Ekom/Front/CategoryController.php(26): Module\Ekom\Model\Front\DynamicProductListModel::getModelByCategorySlug('cross_training')
                             * #12 /Volumes/Macintosh HD 2/web/php/projects/leaderfit/leaderfit/class-core/Controller/ApplicationController.php(34): Controller\Ekom\Front\CategoryController->prepareClaws()
                             * #13 [internal function]: Core\Controller\ApplicationController->renderClaws()
                             * #14 /Volumes/Macintosh HD 2/web/php/projects/universe/planets/Kamille/Architecture/RequestListener/Web/ControllerExecuterRequestListener.php(96): call_user_func(Array)
                             * #15 /Volumes/Macintosh HD 2/web/php/projects/universe/planets/Kamille/Architecture/RequestListener/Web/ControllerExecuterRequestListener.php(62): Kamille\Architecture\RequestListener\Web\ControllerExecuterRequestListener->executeController('Controller\\Ekom...')
                             * #16 /Volumes/Macintosh HD 2/web/php/projects/universe/planets/Kamille/Architecture/Application/Web/WebApplication.php(132): Kamille\Architecture\RequestListener\Web\ControllerExecuterRequestListener->listen(Object(Kamille\Architecture\Request\Web\HttpRequest))
                             * #17 /Volumes/Macintosh HD 2/web/php/projects/kamille-modules/Core/ApplicationHandler/WebApplicationHandler.php(125): Kamille\Architecture\Application\Web\WebApplication->handleRequest(Object(Kamille\Architecture\Request\Web\HttpRequest))
                             * #18 /Volumes/Macintosh HD 2/web/php/projects/leaderfit/leaderfit/www/index.php(26): Module\Core\ApplicationHandler\WebApplicationHandler->handle(Object(Kamille\Architecture\Application\Web\WebApplication))
                             * #19 {main}"
                             * }
                             */
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