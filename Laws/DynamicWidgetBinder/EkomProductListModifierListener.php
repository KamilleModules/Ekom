<?php


namespace Module\Ekom\Laws\DynamicWidgetBinder;


use Kamille\Services\XLog;
use Kamille\Utils\Laws\DynamicWidgetBinder\Listener\DynamicWidgetBinderListenerInterface;

use Module\Ekom\Laws\DynamicWidgetBinder\Attribute2TemplateAdaptor\Attribute2TemplateAdaptorInterface;
use Module\Ekom\Utils\E;

class EkomProductListModifierListener implements DynamicWidgetBinderListenerInterface
{


    public static function create()
    {
        return new static();
    }

    public function decorate($payload, array &$config)
    {

        $adaptor = E::conf("attribute2TemplateAdaptor");
        if (class_exists($adaptor)) {

            /**
             * @var $oAdaptor Attribute2TemplateAdaptorInterface
             */
            $oAdaptor = new $adaptor();


            foreach ($payload as $attrName => $attributes) {

                $widgetInternalName = $attrName . "AttributeSelector";

                $tpl = $oAdaptor->getTemplate($attrName);
                if (false !== $tpl) {

                    $config['sidebar.' . $widgetInternalName] = [
                        "tpl" => $tpl,
                        'conf' => [
                            "attributes" => $payload[$attrName],
                        ],
                    ];
                }
            }

        } else {
            XLog::error("[Ekom module] - EkomProductListModifierListener: class does not exist: $adaptor");
        }
    }
}