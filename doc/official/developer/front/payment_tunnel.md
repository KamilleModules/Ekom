Tunnel de paiement
===================
2018-03-20


> Document en cours de rédaction




Les objets importants: 


- le checkout process (affichage, gestion des steps) est géré par: \Module\Ekom\Utils\CheckoutProcess\EkomCheckoutProcess
- la gestion du paiement de la commande (avec ingenico): class-modules/Ekom/Utils/CheckoutOrder/CheckoutOrderUtil.php
- création de l'échéancier: class-modules/ThisApp/Ekom/PaymentMethodHandler/CreditCardWalletPaymentMethodHandler.php