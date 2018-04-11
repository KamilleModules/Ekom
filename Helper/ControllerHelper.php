<?php


namespace Module\Ekom\Helper;


use Controller\NullosAdmin\Back\NullosStandardPageController;
use Module\Ekom\Exception\EkomException;

class ControllerHelper
{
    /**
     * Backoffice
     */
    public static function addHelpBtn(NullosStandardPageController $controller, string $identifier, string $type = null)
    {
        if (null === $type) {
            $type = "formlist";
        }
        switch ($type) {
            case "formlist":
                $link = "http://www.ling-docs.ovh/ekom/#/glossary/back-formlists-glossary?id=" . $identifier;
                break;
            default:
                throw new EkomException("Unknown help type: $type with identifier $identifier");
                break;
        }

        $controller->pageTop()->rightBar()->addButton("Help", $link, "fa fa-life-buoy", "btn-primary");
    }
}