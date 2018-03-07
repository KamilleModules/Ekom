<?php
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;


?>
<style>
    .core-widget-error-details {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
        margin-top: 10px;
    }

    .core-widget-error-details tr,
    .core-widget-error-details td {
        border: 1px solid black;
    }

    .core-widget-error-details td {
        padding: 5px;
    }

    .core-widget-error-details tr td:first-of-type {
        font-weight: bold;
    }

</style>

<div class="window2" style="margin-top: 30px;">
    <h2 style="font-size: 1em">A problem occurred with this widget.</h2>
    <?php if (true === ApplicationParameters::get("debug")): ?>
        <table class="core-widget-error-details">
            <tr>
                <td>Title</td>
                <td>
                    <?php if (array_key_exists('errorTitle', $v)): ?>
                        <?php echo $v['errorTitle']; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Code</td>
                <td>{errorCode}</td>
            </tr>
            <tr>
                <td>Message</td>
                <td>{errorMessage}</td>
            </tr>
        </table>

    <?php endif; ?>
    <span style="display: none;">
    A problem occurred with this widget, please contact the webmaster.
    I'm the Core widget error template
</span>


</div>