Lee notes
======================
2017-06-08


Here is a typical model for the on-the-fly form in lee.
Used in "theme/lee/widgets/Ekom/CheckoutOnePage/ThreeStepper/default.tpl.php".


(the style will be put in style.scss)


```html

<style>
    .lee-modal {
    }

    .lee-modal .top-bar {
        border-bottom: 1px solid #d8d8d8;
        margin-bottom: 20px;
        padding-bottom: 11px;
        font-weight: bold;
        font-size: 1.2em;
        color: #8c8c8c;
    }

    .lee-modal .bottom-bar {
        margin-top: 30px;
        border-top: 1px solid #d8d8d8;
        padding-top: 15px;
        display: flex;
        justify-content: flex-end;
    }

    .lee-modal .bottom-bar .lee-button {
        display: inline-block;
        padding: 11px;
        border-radius: 5px;
        border: 1px solid #cecece;
        margin-right: 10px;
        cursor: pointer;
        transition: color 300ms ease-in-out, background-color 300ms ease-in-out;
        background-color: #f6f6f6;
        color: #333;
    }

    .lee-modal .bottom-bar .lee-button:hover {
        background-color: #f0f0f0;
        color: #000;
    }

    .lee-modal .bottom-bar .lee-button.validate {
        background-color: #f9df70;
        color: #333;
        font-weight: bold;
    }

    .lee-modal .bottom-bar .lee-button.validate:hover {
        background-color: #ffdc76;
        color: #333;
    }

    .lee-modal .form-table {
        width: 100%;
    }

    .lee-modal .form-table tr {
        height: 40px;
    }

    .lee-modal .form-table tr td:first-of-type {
        text-align: right;
        padding-right: 10px;
    }

    .lee-modal .form-table input {
        width: 100%;
        height: 30px;
        padding: 5px;
    }

    .lee-modal .form-table tr td:first-of-type {
        width: 100px;
    }

</style>
<div style="display:none">
    <div id="modal-address-form" class="lee-modal">
        <div class="top-bar">Saisir une nouvelle adresse de livraison</div>
        <!--        <div class="top-bar">Mettre à jour votre adresse d'expédition</div>-->
        <form style="width: 500px">
            <table class="form-table">
                <tr>
                    <td>Prénom</td>
                    <td><input name="first_name" type="text" value=""></td>
                </tr>
                <tr>
                    <td>Nom</td>
                    <td><input name="last_name" type="text" value=""></td>
                </tr>
                <tr>
                    <td>Adresse</td>
                    <td><input name="address" type="text" value=""></td>
                </tr>
                <tr>
                    <td>Code postal</td>
                    <td><input name="postcode" type="text" value=""></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><select name="country">
                            <?php foreach ($countries as $k => $v):
                                $ssel = ($countryId === $k) ? ' selected="selected"' : '';
                                ?>
                                <option <?php echo $ssel; ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Numéro de téléphone</td>
                    <td><input name="phone" type="text" value=""></td>
                </tr>
                <tr>
                    <td>Code accès de la résidence</td>
                    <td><input name="extra" type="text" value=""></td>
                </tr>
            </table>
        </form>
        <div class="bottom-bar">
            <button class="lee-button validate">Envoyer à cette adresse</button>
            <button class="lee-button">Supprimer cette adresse</button>
        </div>
    </div>
</div>
```