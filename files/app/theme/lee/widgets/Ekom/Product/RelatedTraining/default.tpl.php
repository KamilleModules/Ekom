<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/widget-related-training.css");

$items = $v['relatedTrainings'];


?>

<?php if ($items): ?>

    <div class="widget widget-related-training window2" id="widget-related-training">
        <div class="top-title">
            <span class="main">ILS SE SONT AUSSI INSCRITS</span>
        </div>

        <div class="list-training">
            <?php foreach ($items as $p):
                $info = $p['trainingInfo'];

                ?>

                <div class="item">
                    <div class="left-block">
                        <div class="line">
                            <div class="title"><?php echo $p['label']; ?></div>
                            <div class="duration"><?php echo $info['nb_days']; ?> JOURS
                                - <?php echo $info['nb_days'] * 7; ?>H
                            </div>
                            <div class="price"><?php echo $p['salePrice']; ?></div>
                        </div>
                        <div class="line2">
                            <?php echo $p['description']; ?>
                        </div>
                        <div class="line3">
                            <a href="<?php echo $p['uriCard']; ?>" class="lee-red-button">PLUS D'INFORMATIONS</a>
                        </div>
                    </div>
                    <div class="right-block">
                        <img src="<?php echo $p['imageSmall']; ?>" alt="<?php echo htmlspecialchars($p['label']); ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    </div>
<?php endif; ?>