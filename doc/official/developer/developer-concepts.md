Concepts pour le développement
=======================
2018-03-09






Le système de prix: `Ekom order model`
--------------

Ekom utilise le schéma suivant (appelé `ekom order model`)
pour définir les prix des produits.

<a href="pdf/ekom-order-model10.pdf">Télécharger le pdf</a>

<img src="image/dev/ekom-order-model-X.png" />




Un système multi-vendeurs, pas multi-boutiques
--------------

Ekom de base est un système multi-vendeurs, mais pas multi-boutiques.
C'est à dire que plusieurs vendeurs peuvent vendre sur la même boutique, mais il n'y a qu'une seule boutique à gérer.

Le vendeur est un objet représenté par la table `ek_seller`.


Le fait que ekom soit multi-vendeurs impacte le sytème de gestion des commandes.

En effet, au sein d'une même commande, les produits peuvent appartenir à plusieurs vendeurs différents.
C'est pourquoi ekom utilise une table `ek_order` pour représenter la commande sur le site, et une table `ek_invoice`
représentant les factures émises par le site.


On a une facture par vendeur, c'est à dire que la commande est répartie entre les différents vendeurs, 
et chaque vendeur délivre une (et une seule) facture.

Par exemple, pour la commande n°6, contenant:

- 1 thermomètre (vendeur A)
- 4 serviettes de bain (vendeur B)

on aura 2 factures:

- 1 facture du vendeur A
- 1 facture du vendeur B 