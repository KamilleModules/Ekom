<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("customer/customer-account-menu.css");

?>
<div class="widget widget-customer-account-menu">

    <div class="section">
        <div class="title">MES INFOS PERSONNELLES</div>
        <div class="menu">
            <ul>
                <li><a <?php echo (true === $v['accountInfoActive']) ? 'class="active"' : ''; ?> href="{accountInfo}">Compte</a>
                </li>
                <li><a <?php echo (true === $v['addressBookActive']) ? 'class="active"' : ''; ?>href="{addressBook}">Coordonnées</a>
                </li>
                <li><a <?php echo (true === $v['credentialsActive']) ? 'class="active"' : ''; ?>href="{credentials}">E-mail
                        & mot de passe</>
                </li>
                <li>
                    <a <?php echo (true === $v['paymentMethodActive']) ? 'class="active"' : ''; ?>href="{paymentMethod}">Moyens
                        de paiement</>
                </li>
                <li>
                    <a <?php echo (true === $v['subscriptionManagementActive']) ? 'class="active"' : ''; ?>href="{subscriptionManagement}">Newsletter</a>
                </li>
                <li><a href="#">Mes points fidélité</a></li>
            </ul>
        </div>
    </div>
    <div class="section">
        <div class="title">MES COMMANDES</div>
        <div class="menu">
            <ul>
                <li><a href="#">Mes historiques de commande</a></li>
                <li><a href="#">Mes factures</a></li>
                <li><a href="#">Mes devis</a></li>
                <li><a <?php echo (true === $v['contactSavActive']) ? 'class="active"' : ''; ?> href="{contactSav}">Contacter
                        le SAV</a></li>
            </ul>
        </div>
    </div>
    <div class="section">
        <div class="title">MES FORMATIONS</div>
        <div class="menu">
            <ul>
                <li><a href="#">Mon dossier de formation</a></li>
                <li><a href="#">Mes manuels de formation</a></li>
            </ul>
        </div>
    </div>
    <div class="section">
        <div class="title">MES PRÉFÉRENCES</div>
        <div class="menu">
            <ul>
                <li><a href="#">Mes favoris</a></li>
                <li><a href="#">Mes articles consultés</a></li>
            </ul>
        </div>
    </div>
</div>



