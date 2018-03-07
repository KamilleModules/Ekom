<?php


namespace Theme\Lee\Ekom\ProductBox;

use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Utils\E;
use Module\EkomProductCardVideo\View\EkomProductCardVideoViewHelper;
use Module\ThisApp\ThisAppConfig;
use Theme\LeeTheme;


class GenericProductBoxRenderer
{

    public static function render(array $boxModel)
    {
        ob_start();
        if (array_key_exists('errorCode', $boxModel)) {
            ProductBoxRenderer::create()->renderErroneousBox($boxModel);

        } else {
            switch ($boxModel['seller']) {
                /**
                 * @todo-ling: change seller formation to lf-formation?
                 */
                case ThisAppConfig::SELLER_FORMATION:
                case ThisAppConfig::SELLER_FORMATION_MANU:
                    TrainingProductBoxRenderer::create()->render($boxModel);
                    break;
                case 'lf-events':
                    EventProductBoxRenderer::create()->render($boxModel);
                    break;
                default:
                    ProductBoxRenderer::create()->render($boxModel);
                    break;
            }
        }
        return ob_get_clean();
    }
}