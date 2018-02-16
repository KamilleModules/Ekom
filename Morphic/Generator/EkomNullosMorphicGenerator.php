<?php


namespace Module\Ekom\Morphic\Generator;


use ArrayToString\ArrayToStringTool;
use Bat\CaseTool;
use Bat\FileSystemTool;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\Morphic\Generator\GeneratorHelper\MorphicGeneratorHelper;
use Module\Ekom\Back\Helper\BackHooksHelper;
use Module\Ekom\Morphic\Generator\ConfigFile\EkomFormConfigFileGenerator;
use Module\Ekom\Morphic\Generator\ConfigFile\EkomListConfigFileGenerator;
use Module\NullosAdmin\Morphic\Generator\NullosMorphicGenerator;
use OrmTools\Helper\OrmToolsHelper;
use PhpFile\PhpFile;
use QuickPdo\QuickPdoInfoTool;

class EkomNullosMorphicGenerator extends NullosMorphicGenerator
{


    protected $operations;
    protected $operationsTable2Route;


    public function __construct()
    {
        parent::__construct();
        $this->operations = [];
        $this->operationsTable2Route = [];
        $base = ApplicationParameters::get("app_dir") . "/config/morphic/Ekom/generated";
        $this->configFileDirForm = $base;
        $this->configFileDirList = $base;
        $this->setFormConfigFileGen(EkomFormConfigFileGenerator::create());
        $this->setListConfigFileGen(EkomListConfigFileGenerator::create());
    }


    protected function onGenerateAfter() // override me
    {
        $operations = $this->operations;
        usort($operations, function ($op, $op2) {
            return $op['elementName'] > $op2['elementName'];
        });


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
        foreach ($operations as $c) {


            if ('simple' === $c['elementType']) {

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
            }


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
        FileSystemTool::mkfile($controllerFile, $controllerContent);
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
        $leftTable = OrmToolsHelper::getHasLeftTable($table);
        $file = __DIR__ . "/assets/FormListController.tpl.php";

        // for this shop
        $title = ucfirst($c["elementLabelPlural"]);
        if (in_array("shop_id", $columns)) {
            $title .= " for this shop";
        }


        $begin = '';


        $begin .= '
        
        $route = "' . $route . '";' . PHP_EOL;


        $lastProperties = '';
        $elementType = MorphicGeneratorHelper::getElementType($operation);

        if ('simple' === $elementType) {
            $lastProperties .= '
            "newItemBtnText" => "Add a new ' . $label . '",
            "newItemBtnRoute" => $route,
            ';
        } elseif ("context" === $elementType) {

            $indent = "\t\t";


            $begin .= '
        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
';

            $dbPrefixes = (array_key_exists("dbPrefixes", $config)) ? $config['dbPrefixes'] : [];
            $contextCols = MorphicGeneratorHelper::getContextFieldsByHasTable($table);
            $sArgs = '';
            $count = 0;
            foreach ($contextCols as $col) {
                $begin .= $indent . '$' . $col . ' = $this->getContextFromUrl(\'' . $col . '\');' . PHP_EOL;
                $sArgs .= '$' . $col;
                if (0 !== $count++) {
                    $sArgs .= ', ';
                }
            }


            $begin .= $indent . '$table = "' . $table . '";' . PHP_EOL;
            $begin .= $indent . '$context = [' . PHP_EOL;
            $columnFkeys = $operation['columnFkeys'];
            $sContext = '';
            $sContext2 = '';
            $sContextKeyToForeign = '';
            $sContextKeyToForeignMarkers = '';
            $sLeftArgs = '';
            $sRouteArgs = '';
            $d = 0;
            foreach ($contextCols as $col) {
                if (0 !== $d++) {
                    $sContextKeyToForeign .= ' and ';
                    $sLeftArgs .= '&';
                    $sRouteArgs .= '&';
                }

                $sContext .= $indent . "\t" . '"' . $col . '" => $' . $col . ',' . PHP_EOL;
                $sContext2 .= "\t" . '"' . $col . '" => $' . $col . ',' . PHP_EOL;
                $sContextKeyToForeign .= $columnFkeys[$col][2] . '=:' . $col;
                $sContextKeyToForeignMarkers .= "\t\t" . $indent . '"' . $col . '" => $' . $col . ',' . PHP_EOL;
                $sLeftArgs .= $columnFkeys[$col][2] . '=$' . $col;
                $sRouteArgs .= $col . '=$' . $col;
            }
            $sContext2 .= $indent . "\t\t" . '"avatar" => $avatar' . PHP_EOL;
            $begin .= $sContext;
            $begin .= $indent . '];' . PHP_EOL;


            $begin .= '
        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("' . $leftTable . '");
            $avatar = QuickPdo::fetch("
select $repr from `' . $leftTable . '` where ' . $sContextKeyToForeign . ' 
            ", [
' . $sContextKeyToForeignMarkers . '            
            ], \PDO::FETCH_COLUMN);
        }
            ';


            $zeLeft = $leftTable;
            MorphicGeneratorHelper::dropTablePrefix($zeLeft);
            $leftCamel = CaseTool::snakeToFlexiblePascal($zeLeft);


            $rightTable = OrmToolsHelper::getHasRightTable($table, $dbPrefixes);

            $rightLabel = $this->dictionary->getLabel($rightTable);
            $rightLabelPlural = $this->dictionary->getLabel($rightTable, true);
            $leftLabel = $this->dictionary->getLabel($leftTable);


            $title = ucfirst($rightLabelPlural) . ' for ' . $leftLabel . ' \"$avatar\"';
            $label = $rightLabel . " for " . $leftLabel . ' \"$avatar\"';
            $sourcePage = $leftLabel . ' \"$avatar\"';

            $lastProperties .= '
            "newItemBtnText" => "Add a new ' . $label . '",
            "newItemBtnLink" => E::link("' . $route . '") . "?form&' . $sRouteArgs . '",
            ';


            $leftRoute = $this->operationsTable2Route[$leftTable];
            $lastProperties .= '
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_' . $leftCamel . '_List",             
            "buttons" => [
                [
                    "label" => "Back to ' . $sourcePage . ' page",
                    "icon" => "fa fa-list",
                    "link" => E::link("' . $leftRoute . '") . "?' . $sLeftArgs . '",
                ],
            ],
            "context" => [
            ' . $sContext2 . '
            ],            
            ';

        } else {
            $begin .= '
        //--------------------------------------------
        // SIMPLE PATTERN
        //--------------------------------------------            
            ';
        }


        $content = file_get_contents($file);
        $content = str_replace([
            "ProductGroup",
            '$title',
            '777',
            '$name',
            "// begin",
            "// lastProperties",
        ], [
            $camel,
            $title,
            ArrayToStringTool::toPhpArray($c['ric'], null, 12),
            $c["elementName"],
            $begin,
            $lastProperties
        ], $content);


        return $content;
    }

    protected function onExecuteOperationsBefore(array $operations)
    {
        foreach ($operations as $operation) {
            $this->operationsTable2Route[$operation['elementTable']] = $operation['elementRoute'];
        }
    }
}