<?php


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;


$uriTheme = "/theme/" . ApplicationParameters::get("theme");
//HtmlPageHelper::css($uriTheme . "/css/ultimo.css");

/**
 * Note: most of the html layout comes from the deprecated ultimo theme
 * (i.e. most of the divs here could be removed)
 */

?>
<?php $l->includes("common.php"); ?>
<?php $l->includes("top.php"); ?>

    <div class="main-container col1-layout">
        <div class="main-top-container"></div>
        <div class="main container">
            <div class="inner-container window">
                <div class="preface"></div>
                <div id="page-columns" class="columns">
                    <div class="column-main">
                        <?php $l->position('maincontent'); ?>
                    </div>
                </div>
                <div class="postscript"></div>
            </div>
        </div>
        <div class="main-bottom-container"></div>
    </div>


<?php $l->includes("bottom.php"); ?>