<?php


if (true === $v['isSuccess']) {


    ?>



    <script>
        <?php echo $v['message']; ?>
    </script>


    <?php

} else {
    ?>
    <?php echo $v['message']; ?>
    <?php
}



