<div
        id="widget-create-account-success"
        class="widget widget-create-account-success window"
>
    <p class="central-statement dramatic">
        Votre compte a bien été créé.<br>
        Veuillez lire vos mails pour activer le lien de confirmation que nous vous avons envoyé
        <?php if ($v['email']): ?>
            (à l'adresse <?php echo $v['email']; ?>)
        <?php endif; ?>
        .
    </p>
</div>
