Les objets de l'administration générée
==================
2018-03-07





ek_address
-----------------------

Ce sont des adresses génériques qui peuvent être utilisées par différents objets: des utilisateurs, des vendeurs, des shops.

Une adresse est composée des champs suivants:


- first_name: le prénom
- last_name: le nom de famille
- phone_prefix: le préfixe téléphonique
- phone: le numéro de téléphone (qui suit le préfxe téléphonique)
- address: l'adresse
- city: la ville
- postcode: le code postal
- supplement: un champ supplémentaire pouvant contenir d'autres informations comme: "Sonner à Michel"
- active: Ce champ peut valoir 0, 1 ou 2.
                - 0 signifie que l'adresse n'est pas active
                - 1 signifie que l'adresse est active
                - 2 signifie que l'adresse a été supprimée (vous ne devriez pas vous occuper de cela)
                
- country_id: le pays


!> Pour les adresses vendeur, on utilise le champ last_name pour indiquer le nom de la compagnie (et non pas first_name)



L'objet adresse comporte 3 liens-pivots:

- seller-addresses (ek_seller_has_address): les liaisons entre les vendeurs et les adresses
- shop-addresses (ek_shop_has_address): les liaisons entre les shops et les adresses
- user-addresses: les liaisons entre les users et les adresses


#### ek_seller_has_address


Les adresses des vendeurs.

Les champs de cet objet sont les suivants:


- order: l'ordre d'important de cette liaison
- address: l'adresse
- seller: le vendeur


!> Chaque vendeur ne devrait avoir qu'une seule adresse.
Si vous indiquez plusieurs adresses, l'adresse avec le nombre `order` le plus bas sera utilisée.



#### ek_shop_has_address


Les adresses physiques des shops (magasins).

Cette adresse peut servir à calculer les frais de livraison (certains transporteurs se basent sur l'adresse
d'origine et l'adresse de livraison pour le calcul de leurs frais).


Les champs de cet objet sont les suivants:


- shop id: le shop 
- address: l'adresse
- type: le type d'adresse (arbitraire, mettez ce que vous voulez qui vous permette de mieux vous y retrouver)
- order: n'est pas utilisé pour l'instant


#### ek_user_has_address


Les adresses des utilisateurs.

Chaque utilisateur peut posséder une plusieurs adresse(s).

Dans ekom, chaque adresse peut potentiellement être utilisée comme adresse de livraison ou comme adresse
de facturation, ou même les deux à la fois.

Cela, c'est le client qui le décide au **moment de la commande**.

A partir du moment où l'utilisateur a au moins une adresse, il doit en permanence avoir une adresse
de facturation par défaut ET une adresse de livraison par défaut.


Les champs de cet objet sont:

- order: l'ordre dans lequel les adresses sont affichées dans le carnet d'adresses de l'utilisateur 
- is_default_shipping_address: si cette adresse est l'adresse de livraison par défaut ou pas
- is_default_billing_address: si cette adresse est l'adresse de facturation par défaut ou pas
- address: l'adresse
- user: l'utilisateur




ek_backoffice_user
-----------------------

Cette table n'est plus utilisée.
Elle était à la base prévue pour contenir les utilisateurs du backoffice,
mais finalement c'est la table nul_user qui remplit ce rôle.



ek_carrier
-----------------------

Les transporteurs disponibles.

Les champs sont:

- name: le nom du transporteur, ne mettre que des lettres, des chiffres, et ou des underscores




ek_category
-----------------------

Les catégories du site.

Ces catégories sont utilisées pour classer les `cartes`.
Elles aident également à créer le menu des catégories, ainsi que le fil d'ariane sur certaines pages du site.

Une catégorie contient les champs suivants:

- name: le nom abstrait de la catégorie. Conseil: n'utilisez pas de caractères spéciaux (juste les lettres, les chiffres, le underscore) 
- order: l'ordre d'apparition des catégories dans les menus
- category: la catégorie parente 
- shop: le shop 


Cet objet contient les **liens-pivots** suivants:

- categories (ek_category): le lien vers la catégorie parente
- category-discounts (ek_category_has_discount): les liaisons vers les réductions de catégorie
- category-products cards (ek_category_has_product_card): les liaisons vers les `cartes de produit`
- category langs (ek_category_lang): lien vers la table de traduction des catégories 



#### ek_category


Les catégories parent.

!> Les catégories parent sont elles-mêmes des catégories qui ont des parents et ainsi de suite.


Les champs de cet objet sont les mêmes que ceux énoncés précédemment pour l'objet **ek_category** 
représentant la catégorie fille.


#### ek_category_has_discount

Les liaisons entre les catégories et les réductions.

C'est ici que l'on définit les réductions appliquées aux catégories.

Les champs sont les suivants:

- category: la catégorie sur laquelle appliquer la réduction
- discount: la réduction à appliquer (ek_discount)
- active: est-ce que cette réduction est active (1) ou pas (0) sur le frontoffice
- conditions: définit sous quelles conditions cette réduction s'applique (date, autre).
Ce système n'est pas encore implémenté, et donc la réduction s'applique tout le temps.




