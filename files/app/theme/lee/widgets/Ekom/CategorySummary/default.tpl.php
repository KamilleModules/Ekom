<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/widget-category-summary.css");


$label = $v['label'];
$labelA = substr($label, 0, -3);
$labelB = substr($label, -3);
$cats = $v['cats'];

if ($cats) {
    list($array1, $array2) = array_chunk($cats, ceil(count($cats) / 2));
}
$maxChar = 245;


$nbChar = mb_strlen($v['description']);
$truncated = false;
$descSuite = "";

if ($nbChar > $maxChar) {
    $desc = substr($v['description'], 0, $maxChar);
    $descSuite = substr($v['description'], $maxChar);
    $truncated = true;
} else {
    $desc = $v['description'];
}


?>
<div class="widget widget-category-summary">
    <div class="main-block">
        <h2><?php echo $labelA; ?><span class="colored"><?php echo $labelB; ?></span></h2>

        <div class="description">
            <?php echo $desc; ?>
            <?php if ($truncated): ?>
                <span class="description_suite" id="widget_category_summary_description_suite">
                    <?php echo $descSuite; ?>
                </span>
                <a href="#" class="truncated widget_category_summary_description_suite_toggler"
                   id="widget_category_summary_description_suite_toggler">Lire la suite</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="aux-block">

        <?php if ($cats): ?>
            <div class="col">
                <ul>
                    <?php foreach ($array1 as $item): ?>
                        <li><a href="<?php echo $item['uri']; ?>"><?php echo $item['label']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col">
                <ul>
                    <?php foreach ($array2 as $item): ?>
                        <li><a href="<?php echo $item['uri']; ?>"><?php echo $item['label']; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var isOpened = false;

            $('#widget_category_summary_description_suite_toggler').on('click', function () {

                isOpened = !isOpened;
                $('#widget_category_summary_description_suite').toggle();
                if(false===isOpened){
                    $(this).html('Lire la suite');
                }
                else{
                    $(this).html('Masquer');
                }

                return false;
            });
        });
    });
</script>


