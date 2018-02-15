<?php


namespace Module\Ekom\Morphic\Generator;


use ArrayToString\ArrayToStringTool;
use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\Morphic\Generator\GeneratorHelper\MorphicGeneratorHelper;
use Module\Ekom\Back\Helper\BackHooksHelper;
use Module\Ekom\Morphic\Generator\ConfigFile\EkomFormConfigFileGenerator;
use Module\Ekom\Morphic\Generator\ConfigFile\EkomListConfigFileGenerator;
use Module\NullosAdmin\Morphic\Generator\NullosMorphicGenerator;
use OrmTools\Helper\OrmToolsHelper;
use PhpFile\PhpFile;

class EkomNullosMorphicGenerator extends NullosMorphicGenerator
{


    protected $operations;


    public function __construct()
    {
        parent::__construct();
        $this->operations = [];
        $base = ApplicationParameters::get("app_dir") . "/config/morphic/Ekom/generated";
        $this->configFileDirForm = $base . "/form";
        $this->configFileDirList = $base . "/list";
        $this->setFormConfigFileGen(EkomFormConfigFileGenerator::create());
        $this->setListConfigFileGen(EkomListConfigFileGenerator::create());
    }


    protected function onGenerateAfter() // override me
    {


        $generatedItemFile = BackHooksHelper::getGeneratedMenuLocation();
        $generatedRouteFile = BackHooksHelper::getGeneratedRoutesLocation();
        $menu = PhpFile::create();
        $route = PhpFile::create();
        $menu->addUseStatement(<<<EEE
use Models\AdminSidebarMenu\Lee\Objects\Item;
use Module\NullosAdmin\Utils\N;
EEE
        );
        $menu->addBodyStatement('$generatedItem');
        foreach ($this->operations as $c) {
            //--------------------------------------------
            // CREATE MENU
            //--------------------------------------------
            $menu->addBodyStatement(<<<EEE
    ->addItem(Item::create()
        ->setActive(true)
        ->setName("$c[elementName]")
        ->setLabel("$c[elementLabelPlural]")
        ->setIcon("$c[icon]")
        ->setLink(N::link("$c[elementRoute]"))
    )
EEE
            );


            //--------------------------------------------
            // CREATE ROUTES
            //--------------------------------------------
            $path = 'Controller\Ekom\Back\\Generated\\' . $c['CamelCase'] . '\\' . $c['CamelCase'] . 'ListController';
            $route->addBodyStatement(<<<EEE
\$routes["$c[elementRoute]"] = ["/ekom/generated/$c[elementName]/list", null, null, "$path:render"];
EEE
            );


            //--------------------------------------------
            // CREATE CONTROLLERS
            //--------------------------------------------


        }
        $menu->addBodyStatement(';');
        $menu->render($generatedItemFile);
        $route->render($generatedRouteFile);
    }


    protected function onCreateOperationBefore(array $operation)
    {
        $this->operations[] = $operation;
        $c = $operation;
        $camel = $c['CamelCase'];

        $controllerContent = $this->getControllerContent($c);
        $appRoot = ApplicationParameters::get("app_dir");
        $controllerFile = $appRoot . "/class-controllers/Ekom/Back/Generated/$camel/$camel" . "ListController.php";


        $this->spitOutput($operation);

        if (false === file_exists($controllerFile)) {
            az($controllerFile);
            FileSystemTool::mkfile($controllerFile, $controllerContent);
        }
    }


    protected function spitOutput(array $operation)
    {

        $c = $operation;
        $controllerContent = $this->getControllerContent($c);
        $camel = $c['CamelCase'];
        $controllerBrowserContent = htmlspecialchars('<?') . "php" . substr($controllerContent, 5);


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
        $config = $this->conf;

        $label = $c["elementLabel"];
        $route = $c["elementRoute"];

        $camel = $c['CamelCase'];
        $columns = $operation['columns'];
        $table = $operation['elementTable'];
        $file = __DIR__ . "/assets/FormListController.tpl.php";

        // for this shop
        $labelUcFirst = ucfirst($c["elementLabelPlural"]);
        if (in_array("shop_id", $columns)) {
            $labelUcFirst .= " for this shop";
        }


        $begin = '
$route = "' . $route . '";' . PHP_EOL;



        $lastProperties = '';
        $elementType = MorphicGeneratorHelper::getElementType($operation);

        if ("context" === $elementType) {

            $dbPrefixes = (array_key_exists("dbPrefixes", $config)) ? $config['dbPrefixes'] : [];
            $contextCols = MorphicGeneratorHelper::getContextFieldsByHasTable($table, $dbPrefixes);

            $sArgs = '';
            $c = 0;
            foreach ($contextCols as $col) {
                $begin .= '$' . $col . ' = $this->getContextFromUrl(\'' . $col . '\');' . PHP_EOL;
                $sArgs .= '$' . $col;
                if (0 !== $c++) {
                    $sArgs .= ', ';
                }
            }

            $contextArr = array_map(function ($v) {
                return '$' . $v;
            }, $contextCols);
            $begin .= '$table = "' . $table . '";' . PHP_EOL;
            $begin .= '$context = ' . ArrayToStringTool::toPhpArray($contextArr) . ';' . PHP_EOL;
            $begin .= '
        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $avatar = UserLayer::getUserRepresentationById($id);
        }        
            ';


            $rightTable = OrmToolsHelper::getHasRightTable($table, $dbPrefixes);
            $rightLabel = $this->dictionary->getLabel($rightTable);
            $rightLabel .= ' "\$avatar"';


            $labelUcFirst = ucfirst($c["elementLabelPlural"]);
            $labelUcFirst .= " for " . $rightLabel;
            $label .= " for " . $rightLabel;


            // todo: here...
            $lastProperties = '
            "menuCurrentRoute" => "NullosAdmin_Ekom_User_List___TODO:generatoed",             
            "buttons" => [
                [
                    "label" => "Back to user \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("$route") . "?id=" . $id,
                ],
            ],
            "context" => [
                "id" => $id,
            ],            
            ';

        }


        $content = file_get_contents($file);
        $content = str_replace([
            "ProductGroup",
            '$labelUcFirst', // could be singular form instead
            '$label',
            '777',
            '$name',
            "// begin",
            "// lastProperties",
        ], [
            $camel,
            $labelUcFirst,
            $label,
            ArrayToStringTool::toPhpArray($c['ric'], null, 12),
            $c["elementName"],
            $begin,
            $lastProperties
        ], $content);


        return $content;
    }


}