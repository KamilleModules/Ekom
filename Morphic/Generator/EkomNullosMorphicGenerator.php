<?php


namespace Module\Ekom\Morphic\Generator;


use ArrayToString\ArrayToStringTool;
use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Morphic\Generator\ConfigFile\EkomFormConfigFileGenerator;
use Module\NullosAdmin\Morphic\Generator\NullosMorphicGenerator;

class EkomNullosMorphicGenerator extends NullosMorphicGenerator
{



    public function __construct()
    {
        parent::__construct();
        $this->configFileDir = ApplicationParameters::get("app_dir") . "/config/morphic/Ekom/generated/form";
        $this->setFormConfigFileGen(EkomFormConfigFileGenerator::create());
    }



    protected function onCreateOperationBefore(array $operation)
    {
        $c = $operation;
        $camel = $c['CamelCase'];

        $controllerContent = $this->getControllerContent($c);
        $controllerBrowserContent = htmlspecialchars('<?') . "php" . substr($controllerContent, 5);

        $appRoot = ApplicationParameters::get("app_dir");
        $controllerFile = $appRoot . "/class-controllers/Ekom/Back/$camel/$camel" . "ListController.php";
        if (false === file_exists($controllerFile)) {
            FileSystemTool::mkfile($controllerFile, $controllerContent);
        }


        ?>
        <h1>1. BackHooksHelper</h1>
        <pre>
$item
    ->addItem(Item::create()
        ->setActive(true)
        ->setName("<?php echo $c["elementName"]; ?>")
        ->setLabel("<?php echo $c["elementLabelPlural"]; ?>")
        ->setIcon("<?php echo $c["icon"]; ?>")
        ->setLink(N::link("<?php echo $c["elementRoute"]; ?>"))
    )
</pre>
        <h1>2. back.php</h1>
        <pre>
$routes["<?php echo $c["elementRoute"]; ?>"] = ["/ekom/<?php echo $c["elementName"]; ?>/list", null, null, "Controller\Ekom\Back\<?php
            echo $c['CamelCase']; ?>\<?php echo $camel; ?>ListController:render"];
</pre>
        <h1>3. Controller</h1>
        <pre><?php echo $controllerBrowserContent; ?></pre>
        <h1>4. Breadcrumb (class-modules/Ekom/Back/Config/EkomNullosConfig.php)</h1>
        <pre>
case "<?php echo $c["elementName"]; ?>":
    $item = [
        "label" => "<?php echo ucfirst($c["elementLabel"]); ?>",
        "route" => "<?php echo $c["elementRoute"]; ?>",
    ];
break;
        </pre>


        <?php
    }


    protected function getControllerContent(array $operation)
    {
        $c = $operation;
        $camel = $c['CamelCase'];
        $columns = $operation['columns'];
        $file = __DIR__ . "/assets/SimpleFormListController.tpl.php";

        // for this shop
        $labelUcFirst = ucfirst($c["elementLabelPlural"]);
        if (in_array("shop_id", $columns)) {
            $labelUcFirst .= " for this shop";
        }


        $content = file_get_contents($file);
        $content = str_replace([
            "ProductGroup",
            '$labelUcFirst', // could be singular form instead
            '$label',
            '$route',
            '777',
            '$name',
        ], [
            $camel,
            $labelUcFirst,
            $c["elementLabel"],
            $c["elementRoute"],
            ArrayToStringTool::toPhpArray($c['ric'], null, 12),
            $c["elementName"]
        ], $content);


        return $content;
    }


}