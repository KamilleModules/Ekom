<?php


namespace Module\Ekom\Utils\Morphic;


use Kamille\Utils\Morphic\ListRenderer\MorphicAdminListRenderer;

class EkomMorphicAdminListRenderer extends MorphicAdminListRenderer
{
    public function __construct()
    {
        parent::__construct();
        $this->setWidgetRendererIdentifier('Ekom\Back\GuiAdminTableRenderer\GuiAdminTableWidgetRenderer');
    }
}