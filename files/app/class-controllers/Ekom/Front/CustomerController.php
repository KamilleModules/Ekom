<?php


namespace Controller\Ekom\Front;


use Authenticate\SessionUser\SessionUser;
use Controller\Ekom\EkomFrontController;
use Core\Services\Hooks;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Utils\Claws\ClawsWidget;
use Models\AdminSidebarMenu\Lee\LeeAdminSidebarMenuModel;
use Module\Ekom\Utils\E;


class CustomerController extends EkomFrontController
{

    public function render()
    {
        if (true === SessionUser::isConnected()) {
            return $this->connectedRender();
        } else {
            return $this->requiresConnectedUser();
        }
    }

    public function renderClaws()
    {
        if (true === SessionUser::isConnected()) {
            return parent::renderClaws();
        } else {
            return $this->requiresConnectedUser();
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function connectedRender()
    {
        // override me
    }

    protected function prepareClaws() // override me
    {
        parent::prepareClaws();


        //--------------------------------------------
        // ADD CUSTOMER ACCOUNT MENU, SET THE LAYOUT TEMPLATE
        //--------------------------------------------
        $menu = LeeAdminSidebarMenuModel::create();
        /**
         * modules can feed the menu,
         * but ekom will provide a default menu as well (so that when
         * we install a default ekom, we have the basic functionality out of the box).
         *
         * The modules should have the power to turn down the ekom items that they don't need/like.
         * Therefore, all sections and items provided by ekom will be prefixed with the string "ekom".
         */
        Hooks::call("Ekom_feedCustomerMenu", $menu);

        $claws = $this->getClaws();
        $claws
            ->setLayout("sandwich_2c/account")
            ->setWidget("sidebar.customerAccountMenu", ClawsWidget::create()
                ->setTemplate("Ekom/Customer/CustomerAccountMenu/leaderfit")
                ->setConf([
                    'menu' => $menu->getArray(),
                ])
            );
    }


}