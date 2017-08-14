<?php


namespace Module\Ekom\View\Address;


use OnTheFlyForm\Helper\OnTheFlyFormHelper;

class AddressAjaxFormRenderer
{

    public static function create()
    {
        return new static();
    }

    public function render(array $m)
    {
        ?>
        <form action="" method="post" style="width: 500px" class="table-form">


            <p class="off-success-message off-success-message-container">Success message</p>

            <p class="off-error-message off-error-message-container">Error message</p>


            <?php OnTheFlyFormHelper::generateKey($m); ?>


            <table>
                <tr>
                    <td>Prénom</td>
                    <td>
                        <input name="<?php echo $m['nameFirstName']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valueFirstName']); ?>">
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['nameFirstName']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>Nom</td>
                    <td><input name="<?php echo $m['nameLastName']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valueLastName']); ?>">
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['nameLastName']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>Adresse</td>
                    <td><input name="<?php echo $m['nameAddress']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valueAddress']); ?>">
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['nameAddress']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>Code postal</td>
                    <td><input name="<?php echo $m['namePostcode']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valuePostcode']); ?>">
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['namePostcode']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>Ville</td>
                    <td><input name="<?php echo $m['nameCity']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valueCity']); ?>">
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['nameCity']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>Pays</td>
                    <td><select name="<?php echo $m['nameCountryId']; ?>"
                        >
                            <?php OnTheFlyFormHelper::selectOptions($m['optionsCountryId'], $m['valueCountryId']); ?>
                        </select>
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['nameCountryId']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>Numéro de téléphone</td>
                    <td><input name="<?php echo $m['namePhone']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valuePhone']); ?>">
                    </td>
                </tr>
                <tr class="hidden" data-error="<?php echo $m['namePhone']; ?>">
                    <td></td>
                    <td data-error-text="1" class="error"></td>
                </tr>
                <tr>
                    <td>
                            <span data-tip="Peut être imprimé sur l'étiquette pour faciliter la livraison (par exemple le code d'accès de la résidence)."
                                  class="hint">Informations complémentaires</span>
                    </td>
                    <td><input name="<?php echo $m['nameSupplement']; ?>" type="text"
                               value="<?php echo htmlspecialchars($m['valueSupplement']); ?>"></td>
                </tr>
                <tr>
                    <td colspan="2" class="indent-left">
                        <label>
                            <input type="checkbox" name="<?php echo $m['nameIsDefaultBillingAddress']; ?>"
                                   value="1"
                            >
                            En faire mon adresse de facturation par
                            défaut</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="indent-left">
                        <label>
                            <input type="checkbox" name="<?php echo $m['nameIsDefaultShippingAddress']; ?>"
                                   value="1"
                            >
                            En faire mon adresse de livraison par défaut
                        </label>
                    </td>
                </tr>
            </table>
            <div class="table-form-bottom">
                <button class="lee-red-button create-new-address-btn">Créer cette adresse</button>
                <button class="lee-red-button update-address-btn">Mettre à jour cette adresse</button>
                <button class="lee-black-button close-address-form-btn">Annuler</button>
            </div>
        </form>
        <?php
    }
}