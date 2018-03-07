<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Theme\LeeTheme;

$prefix = "/theme/" . ApplicationParameters::get("theme");

KamilleThemeHelper::css("product-comments.css");
KamilleThemeHelper::css("table-form.css");
LeeTheme::useLib("featherlight");
LeeTheme::useLib('onTheFlyForm');


$isConnected = $v['isConnected'];
$hasComments = ($v["nbComments"] > 0);


$average = round($v['average'] / 20, 1);


?>
<div class="window pt20 widget widget-product-comments"
     style="padding-bottom: 30px;"
     id="widget-product-comments">

    <div class="line top-title">
        <span class="main">AVIS</span>
        <span class="subtitle">{nbComments} avis sur ce produit</span>
    </div>
    <div style="clear: both"></div>

    <?php if (true === $hasComments): ?>
        <div class="line average-line">
            <div class="average-title">
                <h4>Moyenne des notes: <?php echo $average; ?>/5</h4>
            </div>
        </div>
    <?php else: ?>

        <div class="line no-comments-yet">
            <p>
                Aucun avis pour l'instant.<br>
                Soyez le premier à donner votre avis !
            </p>
        </div>
    <?php endif; ?>

    <?php if (false === $isConnected): ?>

        <div class="line line-button">
            <a href="{uriLogin}" class="login-btn">SE CONNECTER</a>
        </div>
    <?php else: ?>
        <div class="line line-button">
            <a href="#" data-featherlight="#write-comment-popup" class="write-comment-btn">ÉCRIRE UN AVIS</a>
        </div>
    <?php endif; ?>


    <?php if (true === $hasComments): ?>
        <div class="line comments-filters">
            <a href="{uriSortByDateDesc}" class="filter <?php echo ('date' === $v['currentSort']) ? 'active' : ''; ?>">LES
                + RÉCENTS</a>
            <a href="{uriSortByUsefulnessDesc}"
               class="filter <?php echo ('useful' === $v['currentSort']) ? 'active' : ''; ?>">LES + UTILES</a>
            <a href="{uriSortByRatingDesc}"
               class="filter <?php echo ('rating' === $v['currentSort']) ? 'active' : ''; ?>">LA NOTE LA + ÉLEVÉE</a>
        </div>

        <div class="comments">
            <?php foreach ($v['comments'] as $item):
                $date = _l()->getLongDate($item['time']);
                ?>
                <div class="comment">


                    <div class="pseudo"><?php echo $item['pseudo']; ?></div>
                    <div class="line rating-line">
                        <div class="rating-container">
                            <div class="rating">
                                <?php
                                $n = ceil($item['rating'] / 20); // 20 = 100 / 5stars
                                for ($i = 5; $i > 0; $i--): ?>
                                    <?php if ((int)$n === $i): ?>
                                        <span class="hover">☆</span>
                                    <?php else: ?>
                                        <span>☆</span>
                                    <?php endif; ?>
                                <?php endfor; ?>

                            </div>
                        </div>
                        <span class="date"><?php echo $date; ?></span>
                    </div>
                    <?php if (!empty($item['title'])): ?>
                        <div class="title"><?php echo $item['title']; ?></div>
                    <?php endif; ?>
                    <div class="comment-text">
                        <p><?php echo $item['comment']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<div class="lee-hidden">
    <div id="write-comment-popup">
        <form action="" method="post" class="table-form">
            <input type="hidden" name="product_id" value="<?php echo $v['product_id']; ?>">

            <div class="table-form-title">
                <h3>Écrivez votre commentaire</h3>
            </div>
            <table>
                <tr>
                    <td>
                        <label>Titre (facultatif)</label>
                    </td>
                    <td>
                        <input type="text" name="title" value="">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Commentaire</label>
                    </td>
                    <td>
                        <textarea name="comment" data-error-popout="comment"></textarea>
                    </td>
                </tr>
                <tr class="error hidden" data-error="comment">
                    <td></td>
                    <td data-error-text="1"></td>
                </tr>
                <tr>
                    <td>
                        <label>Note</label>
                        <span class="hint">(5 est la meilleure)</span>
                    </td>
                    <td>
                        <select name="rating">
                            <option value="0">0</option>
                            <option value="20">1</option>
                            <option value="40">2</option>
                            <option value="60">3</option>
                            <option value="80">4</option>
                            <option value="100">5</option>
                        </select>
                    </td>
                </tr>
            </table>
            <div class="table-form-bottom">
                <button class="comment-cancel-btn cancel-btn">Annuler</button>
                <button type="submit" class="comment-submit-btn submit-btn">Envoyer</button>
            </div>
        </form>
    </div>
</div>


<script>
        jqueryComponent.ready(function () {


            function closeCurrentModal() {
                var current = $.featherlight.current();
                current.close();
            }

            var api = ekomApi.inst();

            $(document).on("click", function (e) {
                var jTarget = $(e.target);
                if (jTarget.hasClass("comment-submit-btn")) {

                    var jForm = jTarget.closest("form");
                    var itemData = jForm.serialize();

                    api.comment.createComment(itemData, function (data) {
                        closeCurrentModal();
                        window.location.reload();
                    }, function (formModel) {
                        window.onTheFlyForm.injectValidationErrors(jForm, formModel);
                    }, function (error) {
                        alert(error);
                    });
                    return false;
                }
                else if (jTarget.hasClass("comment-cancel-btn")) {
                    closeCurrentModal();
                    return false;
                }
            });
        });
</script>