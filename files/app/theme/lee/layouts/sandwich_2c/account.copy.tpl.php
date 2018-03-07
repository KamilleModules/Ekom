<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


$uriTheme = "/theme/" . ApplicationParameters::get("theme");


HtmlPageHelper::addBodyClass(" customer-account-index responsive customer-account  page-layout-2columns-left");
//HtmlPageHelper::css($uriTheme . "/css/ultimo.css");

?>
<?php $l->includes("common.php"); ?>
<?php $l->includes("top.php"); ?>

    <div class="main-container col2-left-layout">
        <div class="main-top-container"></div>
        <div class="main container">
            <div class="inner-container">
                <div class="preface"></div>
                <div id="page-columns" class="columns">
                    <div class="column-main">
                        <?php $l->position('maincontent'); ?>
                    </div>
                    <div class="col-left sidebar sidebar-main">
                        <?php $l->position('sidebar'); ?>
                    </div>
                </div>
                <div class="postscript"></div>
            </div>
        </div>
        <div class="main-bottom-container"></div>
    </div>


<?php $l->includes("bottom.php"); ?>