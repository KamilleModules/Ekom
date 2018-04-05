<?php


namespace Module\Ekom\SokoForm\Controls;


use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use SokoForm\Control\SokoFreeHtmlControl;

class SokoCouponRulesFreeHtmlControl extends SokoFreeHtmlControl
{
    public function __construct()
    {
        parent::__construct();
        ob_start();
        $this->displayGui();
        $this->html = ob_get_clean();
    }


    private function displayGui()
    {
        KamilleThemeHelper::moduleCss("Ekom", "backoffice.css");
        $cssId = StringTool::getUniqueCssId("coupon-");
        ?>
        <div class="coupon-rules-gui" id="<?php echo $cssId; ?>">
            <div class="rule-items-container"></div>
            <button class="add-rule-btn btn btn-sm btn-default"><i class="fa fa-plus"></i> Ajouter une règle</button>
        </div>

        <div class="templates" style="display: none">
            <div class="rule-item rule-item-template">
                <div>
                    <button class="btn btn-sm btn-default delete-rule-btn"><i class="fa fa-remove"></i></button>
                </div>
                <div class="rule-item-main">
                    <p>
                        Le panier doit contenir au moins
                        <input type="text" class="form-control quantity-input" value="1">
                        produit(s) correspondant aux règles suivantes
                    </p>

                    <div class="add-rule-container">
                        <div class="add-rule-text-one">
                            Ajouter une règle qui concerne
                        </div>
                        <select class="form-control criterion-selector">
                            <option value="0">Choisissez...</option>
                            <option value="product">Produits</option>
                            <option value="card">Cartes</option>
                            <option value="attribute">Attributs</option>
                            <option value="category">Catégories</option>
                            <option value="manufacturer">Fabricants</option>
                            <option value="seller">Vendeurs</option>
                        </select>
                        <button class="btn btn-sm btn-default add-criteria-btn">
                            <i class="fa fa-plus"></i>
                            Ajouter
                        </button>
                    </div>


                    <div class="criteria-items-container"></div>

                </div>

            </div>


            <div class="criteria-item criteria-item-template" data-type="something">
                <span class="criteria-label">[Produits]</span>
                <input type="text" disabled value="0" class="values-count">
                <button class="btn btn-sm btn-default choose-criteria-btn"><i class="fa fa-list"></i> Choisissez
                </button>
                <div class="spacer"></div>

                <button class="btn btn-sm btn-default delete-criteria-btn"><i class="fa fa-remove"></i></button>
            </div>
        </div>
        <script>
            jqueryComponent.ready(function () {
                var jGui = $('#<?php echo $cssId; ?>');
                var jRuleItemsContainer = $('.rule-items-container');
                var jRuleItemTemplate = $('.rule-item-template');
                var jCriteriaItemTemplate = $('.criteria-item-template');


                function getClone(jObj) {
                    var jClone = jObj.clone();
                    jClone.removeClass("rule-item-template criteria-item-template");
                    return jClone;
                }


                jGui.on('click', function (e) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass("add-rule-btn")) {
                        var jRuleItem = getClone(jRuleItemTemplate);
                        jRuleItemsContainer.append(jRuleItem);
                        return false;
                    }
                    else if (jTarget.hasClass("delete-rule-btn")) {
                        jTarget.closest('.rule-item').remove();
                        return false;
                    }
                    else if (jTarget.hasClass("delete-criteria-btn")) {
                        jTarget.closest('.criteria-item').remove();
                        return false;
                    }
                    else if (jTarget.hasClass("add-criteria-btn")) {
                        var jCriterionSelector = jTarget.closest('.add-rule-container').find('.criterion-selector');
                        var value = jCriterionSelector.val();
                        if (value !== "0") {
                            var label = jCriterionSelector.find('option:selected').html();
                            var jCriteriaItem = getClone(jCriteriaItemTemplate);
                            var jCriteriaItemContainer = jTarget.closest('.rule-item').find('.criteria-items-container');
                            jCriteriaItemContainer.append(jCriteriaItem);
                            jCriteriaItem.find('.criteria-label').html('[' + label + ']');
                            jCriteriaItem.attr("data-type", value);
                        }
                        return false;
                    }
                    else if (jTarget.hasClass("choose-criteria-btn")) {
                        var jCriteriaItem = jTarget.closest('.criteria-item');
                        var type = jCriteriaItem.attr("data-type");
                        nullosApi.inst().request2Modal("Ekom:back.coupon-tennis." + type, {}, function(jModal){

                        });
                        return false;
                    }
                });


            });
        </script>
        <?php
    }

}