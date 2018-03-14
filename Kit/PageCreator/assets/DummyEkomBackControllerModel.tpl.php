<?php


namespace _controllerNamespace_;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Claws\ClawsWidget;

class _controllerClassname_ extends EkomBackSimpleFormListController
{
    public function render()
    {
        $this->prepareClaws();

        $this->getClaws()
            ->setLayout("admin/default")
            ->setWidget("maincontent.dummay", ClawsWidget::create()
                ->setTemplate("Dummy/default")// theme/$themeName/widgets/Dummy/default.tpl.php
                ->setConf([])
            );

        return parent::doRenderClaws();
    }
}


