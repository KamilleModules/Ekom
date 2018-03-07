<?php


namespace Theme\Lee\Ekom\MiniCart;


class CartItemRendererOld
{

    public static function create()
    {
        return new static();
    }

    public function renderItem(array $item)
    {

        if (array_key_exists('eventDetails', $item)):
            $details = $item['eventDetails'];
            $type = $details['type'];
            ?>
            <div class="ekom-events-items">
                <?php
                if (
                    '1-jour' === $type ||
                    '2-jours' === $type
                ):
                    $key = $item['productDetails']['major']['day'];
                    $label = $details['options'][$key]['label'];
                    ?>
                    <div class="ekom-events-item">
                        <span class="label">Détails: </span>
                        <span class="value"><?php echo $label; ?></span>
                    </div>
                    <?php
                else:
                    $selCourses = $item['productDetails']['minor']['dy'];
                    $days = $details['options']['days'];

                    foreach ($days as $day) {
                        $courses = $day['courses'];
                        foreach ($courses as $course) {
                            $idCourse = $course['id'];
                            if (array_key_exists($idCourse, $selCourses)) {
                                ?>
                                <div class="ekom-events-item">
                                    <span class="label"><?php echo $course['label'] . ': ' . $course['start_time'] . ' - ' . $course['end_time']; ?></span>
                                    <span class="value"><?php echo '( ' . $selCourses[$idCourse] . ' places )'; ?></span>
                                </div>
                                <?php
                            }
                        }
                    }
                endif;
                ?>
            </div>
        <?php elseif (array_key_exists('trainingInfo', $item)): ?>
            <div class="ekom-training-products-items">
                <div class="attribute-list-item">
                    <span class="label">Ville: </span>
                    <span class="value"><?php echo $item['trainingInfo']['selectedCityLabel']; ?></span>
                </div>
                <div class="attribute-list-item">
                    <span class="label">Jour: </span>
                    <span class="value"><?php echo $item['trainingInfo']['selectedDateRangeLabel']; ?></span>
                </div>
            </div>
        <?php endif; ?>
        <?php
    }
}