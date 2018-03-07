<?php


$attr = $v['attributes'];

$label = "";
if (count($attr) > 0) {
    $label = $attr[0]['name_label'];
}


?>

<div class="attribute-selector">
    <div class="name"><?php echo $label; ?></div>
    <div class="attributes">
        <?php foreach ($v['attributes'] as $a): ?>
            <div class="attribute">
                <a href="<?php echo $a['uri']; ?>"><?php echo $a['value_label']; ?> (<?php echo $a['count']; ?>)</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>


