<?php


namespace Theme\Lee\Ekom\Helper;


class ListFilterHelper
{

    public static function renderListFilterTitle($title)
    {
        ?>
        <div class="title listfilter-title"><?php echo $title; ?>
            <span class="listfilter-title-toggler"><i class="lee-icon lee-icon-black-arrow-down"></i></span>
        </div>
        <?php
    }


    public static function renderListFilterTitleOld($title)
    {
        ?>
        <div class="title listfilter-title"><?php echo $title; ?></div>
        <?php
    }

}