<?php


use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;

HtmlPageHelper::addBodyClass("sandwich-2c");

?>
<?php $l->includes('common.php'); ?>
<?php $l->includes('top.php'); ?>


<div class="sandwich2-breadcrumbs">
    <?php $l->position('breadcrumbs'); ?>
</div>
<div class="list-header window">
    <?php $l->position('listheader'); ?>
</div>
<div class="sandwich-columns-container window">


    <div class="sidebar product-list-sidebar">
        <div class="product-list-sidebar-toptext">AFFINER PAR</div>
        <div class="product-list-sidebar-inner">
            <?php $l->position('sidebar'); ?>
        </div>
    </div>

    <div class="maincontent">
        <?php $l->position('maincontent'); ?>
    </div>
</div>
<div class="product-list-footer">
    <?php $l->position('listfooter'); ?>
</div>

<?php $l->includes('bottom.php'); ?>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            // removing border top on very first listfilter-box
            $('.listfilter-box:first').addClass('no-border-top');


            /**
             * implementing toggling behaviour
             */
            $(".listfilter-title").on('click', function () {
                var jBody = $(this).next(".listfilter-body");
                jBody.slideToggle({
                    complete: function () {
                        jBody.closest('.listfilter-box').toggleClass("open");
                    },
                    duration: 1
                });
                return false;
            });


        });
    });
</script>

