<?php


namespace Theme\Lee\Ekom\ProductBox;


use Bat\StringTool;
use Kamille\Services\XLog;
use Theme\Lee\Ekom\ProductBox\Helper\AttributeContainerRenderer;
use Theme\Lee\Ekom\ProductBox\Helper\BionicDetailsHelper;

class TrainingProductBoxRenderer extends ProductBoxRenderer
{

    protected $cssBaseClass;

    public function __construct()
    {
        parent::__construct();
        $this->textDescriptionLink = "Formation détaillée";
        $this->styleDescriptionLink = 'style="margin-top: 5px;"';
        $this->positionRenderStock = 2;
        $this->cssBaseClass = 'training';
        $this->cssWidgetClass = 'widget-box-training';
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function renderRefDebugString(array $box)
    {
        ?>
        (#<?php echo $box['product_id']; ?>
        -<?php echo $box['card_id']; ?>
        -<?php echo $box['trainingInfo']['training_id']; ?>)
        <?php
    }



    protected function renderLineBelowPrice(array $model)
    {
        ?>
        <div class="below-price">
            <a href="<?php echo $model['uriLogin']; ?>">Identifiez-vous en tant que professionnel et payez directement
                en ligne</a>
        </div>
        <?php
    }


    protected function renderBlocBeforeAttributes(array $model)
    {
        if (array_key_exists('trainingInfo', $model)) {

            $info = $model['trainingInfo'];

            ?>
            <div class="training-prerequisites-title">Pré-requis</div>
            <div class="training-prerequisites-text">
                <?php echo $info['prerequisites']; ?>
            </div>
            <?php
        } else {
            XLog::error("[Lee Theme]: TrainingProductBoxRenderer - trainingInfo not found with product " . $model['product_id'] . ", card " . $model['card_id']);
        }
    }


    protected function renderStockText($isAvailable, $quantity, $outOfStockText)
    {
        ?>
        <div class="availability-container availability-<?php echo $this->cssBaseClass; ?> availability-<?php echo (int)$isAvailable; ?>">
            <div class="availability availability-in-stock">
                <span class="virtual-quantity-number"><?php echo $quantity; ?></span>
                places restantes
            </div>
            <div class="availability availability-out-of-stock">
                Aucune place restante
            </div>
        </div>
        <?php
    }


    protected function renderQuantityLine(array $model)
    {
        $qty = (true === $model['quantityInStock']) ? '1' : '0';

        ?>
        <div class="training-quantity-line-title">Nombre de participants</div>
        <div class="line training-quantity-line">
            <div class="quantity">
                <div class="pretty-input-number">
                    <input type="number" class="quantity-input bionic-target" data-id="quantity"
                           value="<?php echo $qty; ?>">
                </div>
            </div>
            <div class="add-to-bookmarks">
                <a class="bookmarks add-to-bookmarks bionic-btn"
                   href="#"
                   data-action="user.addProductToWishlist"
                   data-param-product_id="<?php echo $model['product_id']; ?>"
                    <?php BionicDetailsHelper::renderBionicDetailsMap($model['productDetailsMap']); ?>
                >Ajouter à ma liste</a>
            </div>
            <a href="#" class="download-training-document">Télécharger la Fiche Pédagogique</a>
        </div>
        <?php
    }


    protected function renderBelowPaymentButtons(array $model)
    {
        ?>
        <button class="pay-helpers-training">Facilité de paiement</button>
        <?php
    }

    protected function renderBelowAttributes(array $model)
    {
        $trainingInfo = $model['trainingInfo'];
        $id = StringTool::getUniqueCssId();
        $cityId = 'city-' . $id;
        $dateId = 'date-' . $id;
        ?>

        <div id="<?php echo $id; ?>" class="cell-items-container">
            <?php AttributeContainerRenderer::renderAttributeContainer('Ville', $trainingInfo['cities'], $cityId); ?>
            <?php AttributeContainerRenderer::renderAttributeContainerAsSelect('Date', $trainingInfo['dateRanges'], $dateId); ?>
        </div>

        <?php
    }
}