Les statuts de commande
===================
2018-03-08




Dans Ekom, une commande a toujours un seul statut qui évolue au fur et à mesure que la commande est livrée au client.



Les statuts de commande sont organisés en 3 sections:


- Section principale: 
    - payment_sent: paiement envoyé
    - payment_accepted: paiement accepté
    - payment_verified: paiement vérifié
    - preparing_order: commande en cours de préparation
    - order_shipped: commande envoyée
    - order_delivered: commande livrée
    
- Section erreur
    - payment_error: erreur de paiement
    - preparing_order_error: erreur de préparation de commande
    - shipping_error: erreur de livraison
    - order_delivered_error: erreur de réception de colis
    
- Section management des problèmes 
    - canceled: commande annulée
    - reimbursed: commande remboursée





	
[![ekom-status.jpg](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-status.jpg)](http://lingtalfi.com/img/kamille-modules/Ekom/ekom-status.jpg)
<a href="/pdf/ekom-status.pdf">Télécharger le pdf</a>.





Description des statuts
---------------------


###### Section principale
Cette section regroupe les statuts de commande qui interviennent lors d'une commande normale, c'est à dire sans problèmes particuliers.

###### Section erreur
 Cette section regroupe les accidents qui peuvent survenir pendant le processus de la commande, depuis la commande
sur le site jusqu'à la livraison chez le client.
Généralement, une erreur est temporaire et le statut de commande se résoud vers un statut de la section "management des problèmes"
ou bien de la section principale.

###### Section management des problèmes

Cette section contient les statuts liés au management des problèmes.
    
###### Paiement envoyé
 
Le client a initialisé le paiement, ce qui signifie qu'il a donné son accord pour payer et terminé sa commande sur le front office.
    
######  Paiement accepté
Ce statut est réservé aux méthodes de paiement de type carte bancaire ou paypal qui effectuent une validation en temps
réelle avec la banque du client. 
Le statut paiement accepté est affecté uniquement si la vérification a pu se faire de manière positive avec la banque.
    
###### Paiement vérifié

Le propriétaire de la boutique (ou un outil le représentant) a bien vérifié que l'argent a été transféré sur son compte.
C'est en général à partir de ce moment que le magasin commence à préparer la commande.
    
###### Commande en cours de préparation

Le paiement a bien été vérifié (ou accepté) et le marchand est actuellement en train de préparer la commande.
    
###### Commande envoyée
La commande a été envoyée. Elle arrivera bientôt chez le client.
	
######  Commande livrée
Le transporteur a reporté que la livraison s'est bien effectuée.
	 
######  Erreur de paiement
Le paiement n'a pas pu être vérifié.
	
######  Erreur de préparation de commande
Une erreur s'est produite pendant la phase de préparation de commande.
Par exemple, le magasin découvre qu'il est en rupture de stock pour ce produit.
    
######  Erreur de livraison
Une erreur s'est produite pendant la phase de livraison.
Par exemple, le transporteur n'a pas pu livré le colis à cause d'une adresse inexistante.
    
######  Order de réception de colis
Une erreur s'est produite pendant la phase de réception de colis.
Par exemple, le client refuse un produit endommagé.
	
######  Commande annulée
Ce statut indique que la commande n'a pas été envoyée au client.
C'est un statut final qui termine la chaîne des statuts (c'est à dire que le statut n'évolue plus lorsqu'il atteint
ce statut).
    		
######  Commande remboursée
Ce statut indique que la commande a été remboursée entièrement ou en partie.
C'est un statut final qui termine la chaîne des statuts (c'est à dire que le statut n'évolue plus lorsqu'il atteint ce statut).
	
	
