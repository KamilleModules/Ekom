<?php


namespace Controller\Ekom\Common;


use Bat\UriTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Controller\Web\KamilleController;
use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Architecture\Response\Web\JsonResponse;
use Kamille\Ling\Z;
use Kamille\Services\XLog;

class EkomServiceController extends KamilleController {


    public function render()
    {
        try {

            $serviceId = Z::getUrlParam("serviceIdentifier", null, true);
            $servicesDir = ApplicationParameters::get('app_dir') . "/service";
            $serviceId = UriTool::noEscalating($serviceId);


            $p = explode('/', $serviceId, 3);

            if (3 === count($p)) {

                $___type___ = $p[1];
                if (in_array($___type___, [
                    'ecp',
                    'html',
                ])) {

                    $f = $servicesDir . "/$serviceId.php";
                    if (file_exists($f)) {

                        $type = 'error'; //
                        $out = null;
                        include $f;

                        switch ($___type___) {
                            case 'html':
                                return HttpResponse::create($out);
                                break;
                            case 'ecp':
                                return JsonResponse::create($out);
                                break;
                            default:
                                break;
                        }


                    } else {
                        $this->error("File doesn't exist: $f");
                    }
                } else {
                    $this->error("Don't know how to handle this type: $___type___");
                }
            } else {
                $this->error("serviceId does not respect the scheme: type/Module/serviceName: $serviceId");
            }

        } catch (\Exception $e) {
            XLog::error("$e");
            $msg = $e->getMessage();
            return HttpResponse::create("An error occurred with message $msg, please check the logs for more details");
        }

    }


    private function error($msg)
    {
        throw new \Exception($msg);
    }
}