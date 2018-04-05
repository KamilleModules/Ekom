<?php


namespace Module\Ekom\SokoForm\Controls;


use Bat\StringTool;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use SokoForm\Control\SokoFreeHtmlControl;

/**
 *
 * Syntax reminder for db insertion/extraction
 * ======================
 *
 * The inline version of the rules uses this syntax:
 *
 *
 * - syntax: <rule> (<ruleSep> <rule>)*
 * - ruleSep: the diamond symbol (<>)
 * - rule: <quantity> <:> <criterion>
 * - quantity: number
 * - criterion: <criteria> (<criteriaSep> <criteria>)*
 * - criteriaSep: sharp symbol (#)
 * - criteria: <type> <=> <values>
 * - type: string, one of:
 *      - product
 *      - card
 *      - attribute
 *      - category
 *      - manufacturer
 *      - seller
 * - values: csv of ids of the given type
 *
 *
 *
 *
 */
class SokoCouponRulesFreeHtmlControl extends SokoFreeHtmlControl
{


    protected function getSpecificModel() // override me
    {

        ob_start();
        $this->displayGui();
        $this->html = ob_get_clean();

        $this->properties['html'] = $this->html;
        return parent::getSpecificModel();
    }


    private function displayGui()
    {

        $value = self::uncompile($this->value);

        KamilleThemeHelper::moduleCss("Ekom", "backoffice.css");
        $cssId = StringTool::getUniqueCssId("coupon-");
        $cssId2 = StringTool::getUniqueCssId("coupon-hidden-");
        ?>
        <input id="<?php echo $cssId2; ?>" type="hidden" name="<?php echo htmlspecialchars($this->name); ?>" value="">
        <div class="coupon-rules-gui" id="<?php echo $cssId; ?>">
            <div class="rule-items-container">
                <?php
                foreach ($value as $rule) {
                    self::displayRule([
                        "rule" => $rule,
                    ]);
                }
                ?>
            </div>
            <button class="add-rule-btn btn btn-sm btn-default"><i class="fa fa-plus"></i> Ajouter une règle</button>


            <div class="templates" style="display: none">
                <?php self::displayRule([
                    'isTemplate' => true,
                ]); ?>

                <?php self::displayCriteria([
                    'isTemplate' => true,
                ]); ?>
            </div>
        </div>
        <script>
            jqueryComponent.ready(function () {
                var jGui = $('#<?php echo $cssId; ?>');
                var jInput = $('#<?php echo $cssId2; ?>');
                var jRuleItemsContainer = $('.rule-items-container', jGui);
                var jRuleItemTemplate = $('.rule-item-template', jGui);
                var jCriteriaItemTemplate = $('.criteria-item-template', jGui);


                function getClone(jObj) {
                    var jClone = jObj.clone();
                    jClone.removeClass("rule-item-template criteria-item-template");
                    return jClone;
                }


                function getCriteriaValues(jCriteriaItem) {
                    var ret = [];
                    var sValues = jCriteriaItem.attr('data-values');
                    if ('' !== sValues && sValues) {
                        ret = sValues.split(',');
                    }
                    return ret;
                }

                function compile() {

                    var s = '';
                    var ruleSep = '<>';
                    var criteriaSep = '#';
                    var cpt = 0;


                    jRuleItemsContainer.find(".rule-item").each(function () {
                        var quantity = $(this).find('.quantity-input').val();
                        var criterion = '';
                        var criterionCpt = 0;
                        var jCriteriaItems = $(this).find('.criteria-items-container');

                        jCriteriaItems.find('.criteria-item').each(function () {
                            var type = $(this).attr("data-type");
                            var values = $(this).attr("data-values");
                            var criteria = type + '=' + values;
                            if (0 !== criterionCpt) {
                                criterion += criteriaSep;
                            }
                            criterion += criteria;
                            criterionCpt++;
                        });
                        var rule = quantity + ":" + criterion;

                        if (0 !== cpt) {
                            s += ruleSep;
                        }
                        s += rule;
                        cpt++;
                    });
                    return s;
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
                        var values = getCriteriaValues(jCriteriaItem);

                        var type = jCriteriaItem.attr("data-type");

                        nullosApi.inst().request2Modal("Ekom:back.coupon-tennis", {
                            type: type,
                            values: values
                        }, {
                            onCloseBefore: function (jModal) {
                                var jRightSelect = jModal.find('.right-select');
                                var values = [];
                                jRightSelect.find('option').each(function () {
                                    values.push($(this).val());
                                });

                                var sValues = values.join(',');
                                var count = values.length;

                                jCriteriaItem.find('.values-count').val(count);
                                jCriteriaItem.attr('data-values', sValues);
                            }
                        });
                        return false;
                    }
                });


                jGui.closest('form').on('submit.couponRules', function () {
                    var syntax = compile();
                    jInput.val(syntax);
                });


            });
        </script>
        <?php
    }


    private static function displayCriteria(array $options = [])
    {
        $isTemplate = $options['isTemplate'] ?? false;
        $criteria = $options['criteria'] ?? [];
        $type = "something";
        $values = "";
        $label = "Produits";
        $count = 0;
        if ($criteria) {
            list($type, $values, $count) = $criteria;
            $label = self::getLabelByType($type);
        }

        $sClass = '';
        if (true === $isTemplate) {
            $sClass = 'criteria-item-template';
        }
        ?>
        <div class="criteria-item <?php echo $sClass; ?>" data-type="<?php echo $type; ?>"
             data-values="<?php echo $values; ?>">
            <span class="criteria-label">[<?php echo $label; ?>]</span>
            <input type="text" disabled value="<?php echo $count; ?>" class="values-count">
            <button class="btn btn-sm btn-default choose-criteria-btn"><i class="fa fa-list"></i> Choisissez
            </button>
            <div class="spacer"></div>

            <button class="btn btn-sm btn-default delete-criteria-btn"><i class="fa fa-remove"></i></button>
        </div>
        <?php
    }

    private static function getLabelByType(string $type)
    {
        switch ($type) {
            case "product":
                return "Produits";
                break;
            case "card":
                return "Cartes";
                break;
            case "attribute":
                return "Attributs";
                break;
            case "category":
                return "Catégories";
                break;
            case "manufacturer":
                return "Fabricants";
                break;
            case "seller":
                return "Vendeurs";
                break;
            default:
                break;
        }
    }

    private static function displayRule(array $options = [])
    {
        $isTemplate = $options['isTemplate'] ?? false;
        $rule = $options['rule'] ?? [];
        $quantity = $rule['quantity'] ?? 1;
        $criterion = $rule['criterion'] ?? [];


        $sClass = '';
        if (true === $isTemplate) {
            $sClass = 'rule-item-template';
        }
        ?>
        <div class="rule-item <?php echo $sClass; ?>">
            <div>
                <button class="btn btn-sm btn-default delete-rule-btn"><i class="fa fa-remove"></i></button>
            </div>
            <div class="rule-item-main">
                <p>
                    Le panier doit contenir au moins
                    <input type="text" class="form-control quantity-input" value="<?php echo $quantity; ?>">
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
                <div class="criteria-items-container">
                    <?php foreach ($criterion as $criteria): ?>
                        <?php self::displayCriteria([
                            "criteria" => $criteria,
                        ]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function uncompile($value)
    {

        $ruleSep = '<>';
        $criteriaSep = '#';
        $ret = [];
        if ($value) {

            $rules = explode($ruleSep, $value);
            foreach ($rules as $rule) {
                $p = explode(':', $rule, 2);
                $quantity = $p[0];
                $criterion = $p[1];
                $allCriteria = explode($criteriaSep, $criterion);
                $_criterion = [];
                foreach ($allCriteria as $criteria) {
                    $q = explode('=', $criteria, 2);
                    $type = $q[0];
                    $values = $q[1];
                    $aValues = explode(',', $values);
                    $count = count($aValues);

                    $_criterion[] = [$type, $values, $count];
                }


                $_rule = [
                    "quantity" => $quantity,
                    "criterion" => $_criterion,
                ];

                $ret[] = $_rule;
            }
        }
        return $ret;
    }
}