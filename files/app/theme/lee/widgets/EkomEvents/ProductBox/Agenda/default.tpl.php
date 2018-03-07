<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/widget-product-events.css");

$agenda = $v['agenda'];

?>
<div id="widget-product-events-agenda"
     class="widget widget-product-events-agenda window pt20">


    <div class="line top-title">
        <span class="main">PLANNING</span>
    </div>


    <div class="agenda-columns">
        <?php foreach ($agenda as $day): ?>
            <div class="day">
                <div class="title"><?php echo strtoupper($day['label']); ?></div>
                <div class="hours">
                    <ul>
                        <?php foreach ($day['courses'] as $course): ?>
                            <li>
                                <span class="time"><?php echo $course['start_time']; ?>
                                    - <?php echo $course['end_time']; ?></span>
                                <span class="label"><?php echo ucfirst($course['label']); ?> BY
                                    <?php
                                    $c = 0;
                                    foreach ($course['presenters'] as $pres): ?>
                                        <?php if (0 !== $c++): ?>
                                            ,&nbsp;
                                        <?php endif; ?>
                                        <?php echo $pres['pseudo']; ?>
                                    <?php endforeach; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>