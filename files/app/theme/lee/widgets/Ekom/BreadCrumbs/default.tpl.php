<?php


use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/breadcrumbs.css");


$sep = " / ";
$prev = null;
if (array_key_exists('HTTP_REFERER', $_SERVER)) {
    $prev = $_SERVER['HTTP_REFERER'];
}


?>
<?php if (null !== $v['label']): ?>

    <div class="widget widget-breadcrumbs window">


        <ul>
            <?php if (null !== $prev): ?>
                <li>
                    <span class="sep">&lt;</span>
                    <a href="<?php echo $prev; ?>" class="first-link">RETOUR</a>
                    <span class="sep"><?php echo $sep; ?></span>
                </li>
            <?php endif; ?>



            <?php


            foreach ($v['items'] as $item):
                $label = StringTool::ucfirst($item['label']);
                ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['link']); ?>"
                       title="<?php echo htmlspecialchars($item['title']); ?>"><?php echo $label; ?></a>
                    <span class="sep"><?php echo $sep; ?></span>
                </li>
            <?php endforeach; ?>

            <li>
                <?php echo $v['label']; ?>
            </li>

        </ul>
    </div>
<?php endif; ?>