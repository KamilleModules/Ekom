<?php


namespace Controller\Ekom\Back;


use Bat\ArrayTool;
use Controller\NullosAdmin\NullosBaseController;
use Core\Services\A;
use Kamille\Architecture\Controller\Exception\ClawsHttpResponseException;
use Kamille\Architecture\Response\Web\HttpResponseInterface;
use Kamille\Architecture\Response\Web\RedirectResponse;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\ClawsWidget;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Back\Util\QuickStartWizard\QuickStartWizard;
use Module\Ekom\Back\WidgetModel\ContextBar\ContextBarWidgetModel;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\Utils\N;
use QuickPdo\QuickPdoExceptionTool;
use SokoForm\Form\SokoFormInterface;

class EkomBackController extends NullosBaseController
{


    protected function prepareClaws()
    {
        //--------------------------------------------
        // ENSURE THAT CONTEXT VARS ARE PROPERLY SET
        //--------------------------------------------
        /**
         * The currency, lang, and shop must be defined
         * prior to any other actions.
         */
        $message = null;
        if (false === QuickStartWizard::checkApp($message)) {
            $this->addNotification($message, "error");
        }

        parent::prepareClaws();

        $model = ContextBarWidgetModel::getModel();


        $this->getClaws()
            ->setWidget("topbar_right.ekomContextBar", ClawsWidget::create()
                ->setTemplate('NullosAdmin/TopBar/EkomContextBar/default')
                ->setConf($model)
            );
    }


    protected function handleMorphicForm(array $config, array $options = [])
    {


        $o = array_replace([
            "forceFeed" => false,
        ], $options);

        $process = $config['process'];
        /**
         * @var $form SokoFormInterface
         */
        $form = $config['form'];


        // feeding
        $ric = $config['ric'];
        if (
            ArrayTool::arrayKeyExistAll($ric, $_GET) ||
            true === $o['forceFeed'] ||
            (array_key_exists("forceFeed", $config) && true === $config['forceFeed'])
        ) {
            $feed = $config['feed'];
            call_user_func($feed, $form, $ric);
        }
        try {
            $form->process($process);
        } catch (\Exception $e) {

            if (QuickPdoExceptionTool::isDuplicateEntry($e)) {
                $form->addNotification("This entry already exist in the database", "warning");
            } else {


                if ($e instanceof ClawsHttpResponseException) { // this is a special signal in claws environment, to redirect
                    throw $e;
                }
                $form->addNotification("An error occurred, please check the logs for more info", "error");
                XLog::error("$e");
            }
        }

    }
}