<?php


/**
 * Goal of this widget:
 * =====================
 *
 * Persist the user's listFilter current selection.
 *
 * (Display every element in the uri that is of type listFilter.)
 *
 */


?>


<?php if (count($v['items']) > 0): ?>
    <div class="widget widget-listfilter-summary widget-listfilter">
        <div class="inner">
            <h2 class="top-title">Shop By</h2>
            <div class="box-container">
                <div class="title-box">Currently shopping by:</div>

                <div class="filter-box">
                    <?php foreach ($v['items'] as $name => $filters): ?>

                        <?php foreach ($filters as $info): ?>
                            <div class="filter">
                                <a class="delete-box" href="<?php echo $info['uri']; ?>">
                                    <span class="delete-icon"></span>
                                    <span class="value"><?php echo $info['label']; ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
                <div class="clear-all-box">
                    <a href="{uriClearAll}">Clear All</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
