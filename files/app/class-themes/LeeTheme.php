<?php


namespace Theme;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Services\XLog;
use Kamille\Utils\Laws\Config\LawsConfig;
use Kamille\Utils\Laws\Theme\LawsThemeInterface;

class LeeTheme implements LawsThemeInterface
{

    private static $loaded = [];
    private static $conf = [
        "qtyInputDebounceTime" => 300,
    ];


    public function configureView($viewId, LawsConfig $config)
    {
//        switch ($viewId) {
//            case "Ekom/homePage":
//                $config->replace([
//                    'layout' => [
//                        'tpl' => 'sandwich_1c/ultimo',
//                    ],
//                ]);
//                break;
//            default:
//                break;
//        }
    }


    public static function useLib($libName, $extra = null)
    {
        if (false === array_key_exists($libName, self::$loaded)) {
            $prefixUri = "/theme/" . ApplicationParameters::get("theme");
            self::$loaded[$libName] = true;
            switch ($libName) {
                case 'autocomplete':
                    HtmlPageHelper::js("$prefixUri/libs/auto-complete/jquery.autocomplete.js", null, null, false);
//                    HtmlPageHelper::js("$prefixUri/libs/auto-complete/jquery.autocomplete.min.js", null, null, false);
                    break;
                case 'bionic':
                    HtmlPageHelper::js("$prefixUri/libs/bionic/bionic.js", null, null, false);
                    HtmlPageHelper::js("/modules/Ekom/js/ekom-bionic.js", null, null, false);
                    break;
                case 'checkoutStepper':
                    HtmlPageHelper::js("$prefixUri/libs/checkout-stepper/checkout-stepper.js", null, null, false);
                    break;
                case 'collapsibleBar':
                    HtmlPageHelper::js("$prefixUri/js/collapsible-bar.js", null, null, false);
                    break;
                case 'cloneTemplate':
                    HtmlPageHelper::js("/modules/Ekom/js/cloneTemplate.js", null, null, false);
                    break;
                case 'datepicker':
                    HtmlPageHelper::css("$prefixUri/libs/jquery-datepicker/css/jquery.datepick.css", null);
                    HtmlPageHelper::js("$prefixUri/libs/jquery-datepicker/js/jquery.plugin.js", null, null, false);
                    HtmlPageHelper::js("$prefixUri/libs/jquery-datepicker/js/jquery.datepick.min.js", null, null, false);
                    if (null !== $extra) {
                        HtmlPageHelper::js("$prefixUri/libs/jquery-datepicker/js/jquery.datepick-$extra.js", null, null, false);
                    }
                    break;
                case 'elevateZoom':
                    HtmlPageHelper::js($prefixUri . "/libs/elevate-zoom/jquery.elevateZoom-3.0.8.min.js", null, null, false);
                    break;
                case 'featherlight':
                    HtmlPageHelper::js("$prefixUri/libs/featherlight/featherlight.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/featherlight/featherlight.css", null);
                    break;
                case 'jquery':
                    HtmlPageHelper::js("$prefixUri/libs/jquery/jquery-3.2.1.min.js", "jquery", null, false);
                    break;
                case 'jqueryUiDatePicker':
                    HtmlPageHelper::css("$prefixUri/libs/jquery-ui-datepicker/jquery-ui.min.css", null);
                    HtmlPageHelper::js("$prefixUri/libs/jquery-ui-datepicker/jquery-ui.min.js", null, null, false);
                    if (null !== $extra) {
                        HtmlPageHelper::js("$prefixUri/libs/jquery-ui-datepicker/lang/datepicker-$extra.js", null, null, false);
                    }
                    break;
                case 'jRange':
                    HtmlPageHelper::js("$prefixUri/libs/jRange/jquery.range-min.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/jRange/jquery.range.css", null);
                    break;
                case 'leeCalendar':
                    // depends on jquery
                    HtmlPageHelper::js("$prefixUri/libs/lee-calendar/lee-calendar.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/lee-calendar/lee-calendar.css", null);
                    break;
                case 'myautocomplete':
                    HtmlPageHelper::js("$prefixUri/libs/myautocomplete/myautocomplete.js", null, null, false);
//                    HtmlPageHelper::js("$prefixUri/libs/auto-complete/jquery.autocomplete.min.js", null, null, false);
                    break;
                case 'onTheFlyForm':
                    HtmlPageHelper::js("$prefixUri/libs/on-the-fly-form/on-the-fly-form.js", null, null, false);
                    break;
                case 'phoneCountry':
                    HtmlPageHelper::js("$prefixUri/js/phone-country-widget.js", null, null, false);
                    break;
                case 'prettyCheckbox':
                    // https://lokesh-coder.github.io/pretty-checkbox/#installation
                    HtmlPageHelper::css("https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css", null);
                    break;
                case 'prettyInputNumber':
                    // https://codepen.io/komarovdesign/pen/PPRbgb
                    HtmlPageHelper::css("$prefixUri/libs/pretty-input-number/pretty-input-number.css", null);
                    HtmlPageHelper::js("$prefixUri/libs/pretty-input-number/pretty-input-number.js", null, null, false);
                    break;
                case 'roundCheckbox':
                    HtmlPageHelper::js("$prefixUri/js/round-checkbox.js", null, null, false);
                    break;
                case 'simpleselect':
                    HtmlPageHelper::js("$prefixUri/libs/simple-select/jquery.simpleselect.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/simple-select/jquery.simpleselect.css", null);
                    break;
                case 'slick':
                    HtmlPageHelper::js("$prefixUri/libs/slick/slick.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/slick/slick.css", null);
                    HtmlPageHelper::css("$prefixUri/libs/slick/slick-theme.css", null);
                    break;
                case 'soko':
                    // simple select dependency
                    HtmlPageHelper::js("$prefixUri/libs/simple-select/jquery.simpleselect.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/simple-select/jquery.simpleselect.css", null);

                    HtmlPageHelper::css("$prefixUri/css/soko-form.css", null);
                    HtmlPageHelper::css("$prefixUri/css/notifications.css", null);
                    HtmlPageHelper::js("$prefixUri/js/soko-form-error-removal-tool.js", null, null, false);
                    HtmlPageHelper::js("$prefixUri/js/soko-simple-select.js", null, null, false);
                    break;
                case 'stanConfigurableItems':
                    HtmlPageHelper::js("$prefixUri/libs/stan-configurable-items/stan-configurable-items.js", null, null, false);
                    break;
                case 'tipr':
                    HtmlPageHelper::js("$prefixUri/libs/tipr/tipr.js", null, null, false);
                    HtmlPageHelper::css("$prefixUri/libs/tipr/tipr.css", null);
                    break;
                default:
                    XLog::error('Theme\LeeTheme: Unknown library: ' . $libName);
                    break;
            }
        }
    }

    public static function get($key, $default = null)
    {
        if (array_key_exists($key, self::$conf)) {
            return self::$conf[$key];
        }
        return $default;
    }


}