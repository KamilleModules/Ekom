<?php use Kamille\Utils\Claws\Error\ClawsWidgetError;
use Theme\Lee\WidgetError\WidgetErrorRenderer;

if (false === ClawsWidgetError::modelIsErroneous($v)): ?>
    <div
            id="widget-create-account-success"
            class="widget widget-create-account-success window2"
    >
        <p class="central-statement dramatic">
            Votre compte a bien été activé.<br>
            Merci, et bonne navigation.
        </p>
    </div>
<?php else: ?>
    <div class="window2">
        <?php WidgetErrorRenderer::render($v); ?>
    </div>
<?php endif; ?>
