<?php


use ArrayToString\ArrayToStringTool;
use Kamille\Mvc\Layout\AjaxLayout;
use Kamille\Utils\Claws\Claws;
use Kamille\Utils\Claws\ClawsInterface;
use Kamille\Utils\Claws\ClawsWidget;
use Kamille\Utils\Claws\Renderer\ClawsRenderer;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;

function get($key, $default = false, $isGet = true)
{
    $pool = (true === $isGet) ? $_GET : $_POST;
    if (array_key_exists($key, $pool)) {
        return $pool[$key];
    }
    if (false !== $default) {
        return $default;
    }
    throw new \Exception("Parameter not found in get: $key, get was: " . ArrayToStringTool::toPhpArray($pool));
}


/**
 * @param callable $fn ( ClawsInterface )
 * @return mixed
 */
function renderClaws(callable $fn)
{
    $claws = new Claws();
    call_user_func($fn, $claws);
    $renderer = new ClawsRenderer();
    $renderer->setLayout(AjaxLayout::create());
    return $renderer->setClaws($claws)->render();

}


$s = '';
if (array_key_exists('action', $_GET)) {
    $action = $_GET['action'];
    switch ($action) {
        case "productBoxPopup":
            $pid = get('pid');
            $model = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($pid);
            $s = renderClaws(function (Claws $claws) use ($model) {
                $claws
                    ->setLayout("ajax/default")
                    ->setWidget("main.productBox", ClawsWidget::create()
                        ->setTemplate("Ekom/Product/ProductBox/leaderfit-ajax")
                        ->setConf($model)
                    );
            });
            break;
        case "productBoxPopupItemAdded":
            $pid = get('id', false, false);
            $details = get('details', [], false);
            $model = EkomApi::inst()->productLayer()->getProductBoxModelByProductId($pid, null, null, $details);
            $cartModel = EkomApi::inst()->cartLayer()->getCartModel();

            $itemAdded = [];
            foreach ($cartModel['items'] as $item) {
                if ((int)$pid === (int)$item['product_id']) {
                    $itemAdded = $item;
                    break;
                }
            }


            $s = renderClaws(function (Claws $claws) use ($model, $cartModel, $itemAdded) {
                $claws
                    ->setLayout("ajax/default")
                    ->setWidget("main.productAdded", ClawsWidget::create()
                        ->setTemplate("Ekom/Product/ProductBox/leaderfit-ajax-added")
                        ->setConf([
                            'uriCart' => E::link("Ekom_cart"),
                            'itemAdded' => $itemAdded,
                            'boxModel' => $model,
                            'cartModel' => $cartModel,
                        ])
                    );
            });
            break;
        default:
            break;
    }
}


if ('' !== $s) {
    echo $s;
}