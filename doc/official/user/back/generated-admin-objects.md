Les objets de l'administration générée
==================
2018-03-07


Ci-dessous la description des éléments générés pour le module Ekom.





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

- seller-addresses ([ek_seller_has_address](#ek_seller_has_address)): les liaisons entre les vendeurs et les adresses
- shop-addresses ([ek_shop_has_address](#ek_shop_has_address)): les liaisons entre les shops et les adresses
- user-addresses ([ek_user_has_address](#ek_user_has_address)): les liaisons entre les users et les adresses


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

- categories ([ek_category](#ek_category)): le lien vers la catégorie parente (c'est exactement la même table qui est utilisée)
- category-discounts ([ek_category_has_discount](#ek_category_has_discount)): les liaisons vers les réductions de catégorie
- category-products cards ([ek_category_has_product_card](#ek_category_has_product_card)): les liaisons vers les `cartes de produit`
- category langs ([ek_category_lang](#ek_category_lang)): lien vers la table de traduction des catégories 




ek_category_has_discount
---------------

Les liaisons entre les catégories et les réductions.

C'est ici que l'on définit les réductions appliquées aux catégories.

Les champs sont les suivants:

- category: la catégorie sur laquelle appliquer la réduction
- discount: la réduction à appliquer (ek_discount)
- active: est-ce que cette réduction est active (1) ou pas (0) sur le frontoffice
- conditions: définit sous quelles conditions cette réduction s'applique.
Le [language des conditions](concept/ekom-conditions-language.md) ekom est utilisé.
Le contexte est le suivant (valeurs données à titre d'exemple):
    - date: 2018-03-08
    - product_card_id: 56
    - product_details: []
    - product_id: 56
    - shop_id: 1
    - lang_id: 1
    - currency_id: 1
    - ThisApp_isPro: true
    - ThisApp_userShippingArea: FR
    - ThisApp_userOriginArea: 0    


!> Lorsque vous modifiez une condition, assurez vous de bien rafraîchir le cache du front office également.





ek_category_has_product_card
-------------------

Les liaisons entre les catégories et les `cartes`.

Les champs sont:

- category: la catégorie
- product card: la carte






ek_category_lang
------------------

Les traductions pour les catégories.


Les champs sont:

- category: la catégorie
- lang: la langue
- label: le label de la catégorie (le nom de la catégorie sur le front)
- description: la description de la catégorie
- slug: le morceau d'url utilisé pour les liens vers cette catégorie
- meta title: le contenu de la balise meta title
- meta description: le contenu de la balise meta description
- meta keywords: le contenu de la balise meta keywords






ek_country
-----------------------

Les pays disponibles.
Les champs disponibles sont:

- iso_code: le code iso du pays (ISO 3166-1 alpha2 code https://en.wikipedia.org/wiki/ISO_3166-1)


Les liens-pivots pour cet éléments sont:

- addresses ([ek_address](#ek_address)): les adresses liées à un pays donné (voir ek_address)
- country langs ([ek_country_lang](#ek_country_lang)): les traductions pour ce pays


ek_country_lang
---------------------

Les traductions pour un pays donnée.
Les champs sont:

- label: le label du pays
- country: le pays
- lang: la langue





ek_coupon
-----------------------

Les coupons sont des réductions qui s'appliquent à l'ensemble d'un panier.

Pour bénéficier d'un coupon, l'utilisateur doit entrer un code au moment de passer sa commande.

Les champs sont:

- code: le code à utiliser
- active: est-ce que le coupon est actif (1) ou pas (0). Note: si le client ajoute un coupon
dans son panier et que le modérateur le rend inactif par la suite, le coupon du client sera encore actif.
Autrement dit, la notion d'actif/inactif ne concernent que les coupons qui ne sont pas encore ajoutés
au panier.
- procedure type: définit le type de réduction à appliquer:
    - fixed: une réduction fixe
    - percent: un pourcentage du prix original
    - ...: d'autres types de réduction sont envisageables, voir avec les développeurs 
- procedure operand: la valeur à utiliser en conjonction avec le champ procedure type
- target: n'est plus utilisé pour l'instant



Les liens-pivots pour les coupons sont les suivants:

- coupon langs ([ek_coupon_lang](#ek_coupon_lang)): les traductions pour le coupon donné


ek_coupon_lang
-----------------

Les traductions our les coupons.
Les champs sont:

- label: la traduction (par exemple: "coupon abc")
- coupon: le coupon à utiliser
- lang: la langue à utiliser



ek_currency
-----------------------

Les devises.
Les champs sont:

- iso_code: le code iso de la devise (iso code 4217: https://en.wikipedia.org/wiki/ISO_4217)
- symbol: le symbole de la devise (par exemple: "€")


Les liens-pivots pour les devises sont:

- shops ([ek_shop](#ek_shop)): les magasins ayant la devise donnée comme devise par défaut (voir ek_shop dans ce document)
- shop-currencies ([ek_shop_has_currency](#ek_shop_has_currency)): liaisons entre les shops et les devises





ek_discount
----------------------

Les réductions.

Les champs sont:

- type: la manière dont est affectée le prix. Les valeurs possibles sont:
        - percent: pourcentage
        - amount: valeur fixe
- operand: la valeur à utiliser avec le champ type
- target: la cible sur laquelle s'applique la réduction, les valeurs possibles sont:
        - priceWithTax: le prix TTC
        - priceWithoutTax: le prix HT
- shop: le shop


Les liens-pivots sont les suivants:


- category-discounts ([ek_category_has_discount](#ek_category_has_discount)): liaisons entre les catégories et les réductions
- discount-langs ([ek_discount_lang](#ek_discount_lang)): les traductions des réductions
- product card-discounts ([ek_product_card_has_discount](#ek_product_card_has_discount)): liaisons entre les `cartes` et les réductions
- product-discounts ([ek_product_has_discount](#ek_product_has_discount)): liaisons entre les produits et les réductions






ek_discount_lang
----------------------


Les traductions des réductions.

Les champs sont:

- discount: la réduction
- lang: la langue
- label: la traduction





ek_feature
----------------------


Les caractéristiques d'un produit.

Les champs sont:

- id: (l'identifiant numérique de) la caractéristique produit


Les liens-pivots sont:

- feature-langs ([ek_feature_lang](#ek_feature_lang)): les traductions des caractéristiques produit
- feature-values ([ek_feature_value](#ek_feature_value)): la valeur associée à cette caractéristiques 
- product-features ([ek_product_has_feature](#ek_product_has_feature)): liaisons entre les produits et les caractéristiques



ek_feature_lang
-----------------

Les traductions des caractéristiques produit.

Les champs sont:

- feature: la caractéristique produit
- lang: la langue
- name: le nom symbolique de la caractéristique (pas de caractère exotiques, c'est à dire juste des lettres et chiffres, et underscore)






ek_feature_value
-----------------

Les valeurs associées aux caractéristiques produit.

Les champs sont:

- id: (l'identifiant numérique de) la caractéristique produit
- feature: la valeur de la caractéristique produit


Les liens-pivots sont:

- feature value langs ([ek_feature_value_lang](#ek_feature_value_lang)): les traductions des valeurs de caractéristique-produit
- product-features ([ek_product_has_feature](#ek_product_has_feature)): liaisons entre les produits et les caractéristiques produit ET les valeurs de ces caractéristiques





ek_feature_value_lang
-----------------

Les traductions des valeurs de caractéristiques produit.

Les champs sont:

- feature value: la valeur de la caractéristique produit
- lang: la langue
- value: la traduction de la valeur de la caractéristique produit


ek_invoice
-----------------

Les factures.

Les champs visibles sont:

- shop:: le shop
- user: le client
- order: la commande 
- seller: le vendeur 
- label: le label de la facture (par exemple: paiement n°1 sur 3). N'est pas utilisé pour l'instant. 
- invoice_number: le numéro de la facture 
- invoice_number_alt: le numéro de facture alternatif. Ce champ est utile uniquement quand vous utilisez plusieurs systèmes de numérotation de facture en parallèle.
Par exemple, si vous utilisez un logiciel ERP pour gérer votre facturation. Ce champ vous permet de maintenir la correspondance entre les factures générées par votre ERP
et les factures générées par Ekom.
- invoice_date: la date d'émission de la facture
- payment_method: le nom symbolique de la méthode paiement utilisée
- currency_iso_code: le [code iso de la devise](concept/iso-codes.md) utilisée
- lang_iso_code: le [code iso de la langue](concept/iso-codes.md) utilisée
- shop_host: le nom de domaine du shop
- track_identifier: l'identifiant de tracking permettant à l'utilisateur de suivre l'expédition du colis
- amount: le montant de la facture
- seller: le vendeur



Les liens-pivots sont:

- payments ([ek_payment](#ek_payment)): les paiements associés à cette facture



ek_lang
------------

Les langues.

Les champs sont:

- label: le label de la langue
- iso_code: l'[iso code de la langue](concept/iso-codes.md)

Les liens-pivots sont:

- category langs ([ek_category_lang](#ek_category_lang)): les traductions de catégories associées à cette langue
- country langs ([ek_country_lang](#ek_country_lang)): les traductions de pays associées à cette langue
- coupon langs ([ek_coupon_lang](#ek_coupon_lang)): les traductions de coupons associées à cette langue
- discount langs ([ek_discount_lang](#ek_discount_lang)): les traductions de réductions associées à cette langue
- feature langs ([ek_feature_lang](#ek_feature_lang)): les traductions de caractéristiques produit associées à cette langue
- feature value langs ([ek_feature_value_lang](#ek_feature_value_lang)): les traductions de valeur de caractéristiques produit associées à cette langue
- order status langs ([ek_order_status_lang](#ek_order_status_lang)): les traductions de statuts de commande associées à cette langue
- product attribute langs ([ek_product_attribute_lang](#ek_product_attribute_lang)): les traductions d'attributs de produits associées à cette langue
- product attribute value langs ([ek_product_attribute_value_lang](#ek_product_attribute_value_lang)): les traductions des valeurs d'attributs de produits associées à cette langue
- product card langs ([ek_product_card_lang](#ek_product_card_lang)): les traductions des cartes associées à cette langue
- product langs ([ek_product_lang](#ek_product_lang)): les traductions des produits associées à cette langue
- shops ([ek_shop](#ek_shop)): les shops dont la langue par défaut correspond à cette langue
- shop-langs ([ek_shop_has_lang](#ek_shop_has_lang)): les liaisons entre les shops et langues contenant cette langue
- shop-product card langs ([ek_shop_has_product_card_lang](#ek_shop_has_product_card_lang)): les traductions des liaisons shop-cartes associées à cette langue
- shop-product langs ([ek_shop_has_product_lang](#ek_shop_has_product_lang)): les traductions des liaisons shop-produits associées à cette langue
- tags ([ek_tag](#ek_tag)): les tags associés à cette langue
- tax langs ([ek_tax_lang](#ek_tax_lang)): les traductions des taxes associées à cette langue




ek_manufacturer
------------------

Les fabricants de produits.

Les champs sont les suivants:

- shop: le shop
- name: le nom symbolique (juste lettres, chiffres et underscore) du fabricant


 
Les liens-pivots sont:

- shop-products ([ek_shop_has_product](#ek_shop_has_product)): les liaisons shop-produit dans lesquelles se trouve le manufacturer choisi




ek_newsletter
---------------

La liste des personnes inscrites à la newsletter.

Les champs sont les suivants:

- shop: le shop
- email: l'email
- subscribe_date: la date à laquelle la personne s'est inscrite à la newsletter
- unsubscribe_date: la date à laquelle la personne s'est désinscrite de la newsletter
- active: est-ce que l'on doit envoyer les newsletter à cette personne (1) ou pas (0)





ek_order
------------

Les commandes.

Les champs sont les suivants:

- shop: le shop
- user: le client
- reference: la référence de la commande
- date: la date de la commande
- amount: le montant de la commande
- coupon_saving: le montant économisé grâce aux [coupons](user/discounts-and-coupons.md)
- cart_quantity: la quantité commandée
- currency_iso_code: la devise utilisée pour payer cette commande
- payment_method: le nom symbolique (lettres, chiffres, underscore) de la méthode de paiement utilisée
- payment_method_extra: un complément d'informations pour le champ payment_method.
Par exemple, si le champ payment_method représente la carte bancaire, on pourra utiliser le champ
payment_method_extra pour préciser si le client a opté pour le paiement en une fois, trois fois, quatre fois, ...



Les liens-pivots sont:

- invoices ([ek_invoice](#ek_invoice)): la/les facture(s) correspondant à cette commande
- order-order statuses ([ek_order_has_order_status](#ek_order_has_order_status)): les liaisons entre les commandes et les statuts de commande (pour la commande choisie)  







ek_order_status
-------------------

Les [statuts de commande](concept/order-statuses.md).

Les champs sont:

- code: le code représentant le statut de la commande
- color: le code couleur associé à ce statut
- shop: le shop



Les liens-pivots sont:

- order-order statuses ([ek_order_has_order_status](#ek_order_has_order_status)): les liaisons entre les commandes et les statuts de commande
- order status langs ([ek_order_status_lang](#ek_order_status_lang)): les traductions de statuts de commande 



ek_order_has_order_status
----------------------

Les liaisons entre les commandes et les statuts de commande.

Les champs sont les suivants:

- order: la commande
- order status: le statut de la commande 
- date: la date à laquelle le statut a été affecté à la commande 
- extra: un champ non utilisé pour l'instant




ek_order_status_lang
---------------

Les traductions des statuts de commande.

Les champs sont:

- order status: le statut de commande
- lang: la langue
- label: la traduction



ek_password_recovery_request
-----------------

Les requêtes de renvoi de mot de passe.

Les champs sont les suivants:

- user: le client
- date_created: la date à laquelle la demande de renvoi de mot de passe a été effectuée
- code: un code généré par ekom pour vérifier l'identité du client ayant effectué la demande de mot de passe
- date_used: la date à laquelle le client à cliqué sur le lien du mail permettant de réinitialiser le mot de passe 







ek_payment
------------

Les paiements effectués via le front.
Chaque paiement est associé à une facture.


Les champs sont:

- invoice: la facture
- date: la date du paiement
- paid: est-ce que le paiement a été effectué (1) ou pas (0)
- feedback_details: des informations collectées lors des échanges avec les tiers externes (partenaires banquiers)
- amount: le montant du paiement





ek_payment_method
------------

Les méthodes de paiement disponibles.

Les champs sont les suivants:

- name: le nom symbolique (lettres, chiffres, underscore) de la méthode de paiement



Les liens-pivots sont:

- shop-payment methods ([ek_shop_has_payment_method](#ek_shop_has_payment_method)): les liaisons entre les shops et les méthodes de paiement  






ek_product_attribute_lang
----------------------

Les traductions d'attributs de produits.

Les champs sont:

- product attribute: l'attribut de produit
- lang: la langue
- name: la traduction de cet attribut de produit


ek_product_attribute_value_lang
-----------------

Les traductions de valeurs d'attributs de produits.

Les champs sont:

- product attribute value: la valeur d'attribut de produit
- lang: la langue
- value: la traduction de cette valeur d'attribut de produit




ek_product
-------------------

Un produit du [catalogue général](concept/concept-base.md#le-catalogue-général).


Les champs sont les suivants:

- reference: la référence du produit (Notez qu'on peut redéfinir cette référence au niveau du shop, il est même recommandé de le faire)
- weight: le poids
- price: le prix
- product card: la carte contenant ce produit. Cette carte doit exister avant de pouvoir ajouter le produit.
- TODO here





ek_product_bundle
-------------------

Les [packs](concept/bundle.md).


Les champs sont les suivants:

- id: identifiant numérique du pack
- active: est-ce que le pack est actif (1) ou pas (0)



Les liens-pivots sont:

- product bundle-products ([ek_product_bundle_has_product](#ek_product_bundle_has_product)): les liaisons entre les packs et les produits 



ek_product_bundle_has_product
------------------

Les liaisons entre les [packs](concept/bundle.md) et les produits.

Les champs sont les suivants:

- product bundle: le pack
- product: le produit
- quantity: la quantité de ce produit dans le pack








ek_product_card_has_discount
------------------------

Les liaisons entre les `cartes` et les réductions.

Les champs sont les suivants:

- product card: la carte
- discount: la réduction
- conditions: définit sous quelles conditions cette réduction s'applique.
Le [language des conditions](concept/ekom-conditions-language.md) ekom est utilisé.
- active: est-ce que la réduction est active (1) ou inactive (0)




ek_product_card_lang
------------------------

Les traductions de cartes du [catalogue général](concept/concept-base.md#le-catalogue-général).

Les champs sont les suivants:

- product card: la carte
- lang: la langue
- label: la traduction pour la langue choisie
- description: la description
- slug: le morceau d'url par défaut à afficher pour ce produit. Note: en général ce slug est redéfini au niveau des shops
- meta_title: le meta title 
- meta_description: le meta description 
- meta_keywords: les key words 




ek_product_comment
------------------------

Les commentaires de produits (créés par les clients).

Les champs sont les suivants:

- shop: le shop
- product: le product
- user: le client
- date: la date d'écriture du commentaire
- rating: la note sur 100 donnée par le client au produit
- useful_counter: un indicateur interne permettant de savoir si les autres clients ont trouvé ce commentaire utile ou pas.
Vous ne devriez pas y toucher manuellement.
- title: le titre
- comment: le commentaire
- active: est-ce que le commentaire est actif (1) ou pas (0)







ek_product_has_discount
------------------------

Les liaisons entre les produits et les réductions.

Les champs sont les suivants:

- product: le produit
- discount: la réduction
- conditions: définit sous quelles conditions cette réduction s'applique.
Le [language des conditions](concept/ekom-conditions-language.md) ekom est utilisé.
- active: est-ce que la réduction est active (1) ou inactive (0)



ek_product_has_feature
------------------------

Les liaisons entre les produits, les caractéristiques produit, ET les valeurs de ces caractéristiques produit.

Les champs sont:

- product: le produit
- feature: la caractéristique produit
- shop: le shop
- feature value: la valeur de la caractéristique produit
- position: définit l'ordre d'affichage des caractéristique produit sur le front office
- technical description: la description de la caractéristique produit





ek_product_lang
------------------------

Les traductions de produits du [catalogue général](concept/concept-base.md#le-catalogue-général).

Les champs sont les suivants:

- product: le produit
- lang: la langue
- label: la traduction pour la langue choisie
- description: la description
- meta_title: le meta title 
- meta_description: le meta description 
- meta_keywords: les key words 




ek_product_type
----------------

Les types de produit.
Par défaut, tous les produits ont un type nommé "default".

Les types de produit aident les modules à développer leur logique.
En tant qu'administrateur, vous n'aurez normalement pas besoin de vous servir manuellement
de cette table.

Les champs sont:

- name: le nom symbolique (lettres, chiffres et underscore) du type de produit
- shop: le shop



Les liens-pivots sont les suivants:

- shop-products ([ek_shop_has_product](#ek_shop_has_product)): les liaisons "shop-produit" contenant le type de produit choisi




ek_provider
--------------

Les fournisseurs.

Les champs sont les suivants:

- shop: le shop
- name: le nom symbolique (lettres, chiffres et underscore) du fournisseur



Les liens-pivots sont les suivants:

- shop-product-providers ([ek_shop_has_product_has_provider](#ek_shop_has_product_has_provider)): les liaisons entre les "liaisons shop-produit" et les fournisseurs




ek_seller
--------------

Les vendeurs.

Les champs sont les suivants:

- name: le nom symbolique (lettres, chiffres et underscore) du vendeur
- shop: le shop



Les liens-pivots sont les suivants:

- invoices ([ek_invoice](#ek_invoice)): les factures contenant ce vendeur
- seller-addresses ([ek_seller_has_address](#ek_seller_has_address)): les liaisons entre les vendeurs et les adresses pour ce vendeur
- shop-products ([ek_shop_has_product](#ek_shop_has_product)): les liaisons entre les shops et les produits





ek_seller_has_address
------------------------


Les adresses des vendeurs.

Les champs de cet objet sont les suivants:


- order: l'ordre d'important de cette liaison
- address: l'adresse
- seller: le vendeur


!> Chaque vendeur ne devrait avoir qu'une seule adresse.
Si vous indiquez plusieurs adresses, l'adresse avec le nombre `order` le plus bas sera utilisée.





ek_shop
------------

Les [shops](concept/concept-base.md#multi-shop-multi-lang).


Les champs sont les suivants:

- label: un identifiant pour identifier le shop dans le backoffice
- host: le nom de domaine qui pointera vers le shop. Note: la détection de ce nom de domaine permet d'identifier le shop sur le front, il faut donc bien renseigner cette valeur.
- lang: la langue par défaut du front office
- currency: la devise par défaut pour le front office
- base_currency: la devise de base (utilisée lors de la création des produits/cartes). Son taux d'échange vaudra 1 (c'est la monnaie étalon pour ce shop).
- timezone: le fuseau horaire à utiliser pour le frontoffice et le backoffice 



Les liens-pivots sont les suivants:

- categories ([ek_category](#ek_category)): les catégories appartenant à ce shop
- coupons ([ek_coupon](#ek_coupon)): les [coupons](concept/discounts-and-coupons.md) appartenant à ce shop
- discounts ([ek_discount](#ek_discount)): les [réductions](concept/discounts-and-coupons.md) appartenant à ce shop
- invoices ([ek_invoice](#ek_invoice)): les factures appartenant à ce shop
- manufacturers ([ek_manufacturer](#ek_manufacturer)): les fabricants appartenant à ce shop
- orders ([ek_order](#ek_order)): les commandes appartenant à ce shop
- order statuses ([ek_order_status](#ek_order_status)): les status de commande appartenant à ce shop
- product bundles ([ek_product_bundle](#ek_product_bundle)): les produits recommandés appartenant à ce shop
- product comments ([ek_product_comment](#ek_product_comment)): les commentaires de produit appartenant à ce shop
- product groups ([ek_product_group](#ek_product_group)): les groupes de produits appartenant à ce shop
- product-features ([ek_product_has_feature](#ek_product_has_feature)): les groupes de produits appartenant à ce shop
- product types ([ek_product_type](#ek_product_type)): les types de produit appartenant à ce shop
- providers ([ek_provider](#ek_provider)): les fournisseurs appartenant à ce shop
- sellers ([ek_seller](#ek_seller)): les vendeurs appartenant à ce shop
- shop configuration ([ek_shop_configuration](#ek_shop_configuration)): la configuration du shop
- shop-addresses ([ek_shop_has_address](#ek_shop_has_address)): les liaisons shop-adresse appartenant à ce shop
- shop-carriers ([ek_shop_has_carrier](#ek_shop_has_carrier)): les liaisons shop-transporteur appartenant à ce shop
- shop-currencies ([ek_shop_has_currency](#ek_shop_has_currency)): les liaisons shop-devise appartenant à ce shop
- shop-langs ([ek_shop_has_lang](#ek_shop_has_lang)): les liaisons shop-langue appartenant à ce shop
- shop-payment methods ([ek_shop_has_payment_method](#ek_shop_has_payment_method)): les liaisons shop-méthode de paiement appartenant à ce shop
- shop-products ([ek_shop_has_product](#ek_shop_has_product)): les liaisons shop-produit appartenant à ce shop
- shop-product cards ([ek_shop_has_product_card](#ek_shop_has_product_card)): les liaisons shop-cartes appartenant à ce shop
- tax groups ([ek_tax_group](#ek_tax_group)): les groupes de taxe appartenant à ce shop
- users ([ek_user](#ek_user)): les utilisateurs appartenant à ce shop
- user groups ([ek_user_group](#ek_user_group)): les groupes d'utilisateurs appartenant à ce shop




ek_shop_configuration
------------------

La configuration des shops.

Cette table n'est actuellement pas utilisée.

Les champs sont les suivants:

- key: le nom d'une variable
- value: la valeur de la variable correspondante






ek_shop_has_address
------------------------


Les adresses physiques des shops (magasins).

Cette adresse peut servir à calculer les frais de livraison (certains transporteurs se basent sur l'adresse
d'origine et l'adresse de livraison pour le calcul de leurs frais).


Les champs de cet objet sont les suivants:


- shop id: le shop 
- address: l'adresse
- type: le type d'adresse (arbitraire, mettez ce que vous voulez qui vous permette de mieux vous y retrouver)
- order: n'est pas utilisé pour l'instant




ek_shop_has_carrier
------------------

Les liaisons entre les shops et les transporteurs.

Les champs sont les suivants:

- shop: le shop
- carrier: le transporteur
- priority: la priorité entre les transporteurs. Le transporteur ayant le nombre le plus petit sera prioritaire (affiché avant, et choisi de préférence en cas d'égalité). 







ek_shop_has_currency
----------------

Les liaisons entre les shops et les devises.
Les champs sont:

- shop: le shop
- currency: la devise
- exchange rate: le taux de conversion de cette devise par rapport à la devise de base définie dans `ek_shop.base_currency_id`.
On peut utiliser cet outil pour trouver les taux de conversion entre les devises (Note: http://www.xe.com/currencyconverter/)
- active: est-ce que cette liaison est active (1) ou inactive (0)




ek_shop_has_lang
------------------

Les liaisons entre les shops et les langues.

Les champs sont les suivants:

- shop: le shop
- lang: la langue



ek_shop_has_payment_method
----------------

Les liaisons entre les shops et les méthodes de paiement.


!> Pour que le site fonctionne correctement, il faut qu'il y ait au moins une liaison (entre un shop et une méthode de paiement) dans cette table.


Les champs sont les suivants:

- shop: le shop
- payment method: la méthode de paiement
- order: l'ordre dans lequel sont affichées les méthodes de paiement en général.
La méthode de paiement ayant le chiffre le plus bas est considérée comme la méthode de paiement par défaut.
- configuration: ce champ est utilisé en interne par les modules pour passer des variables de configuration comme la clé Paypal lors du paiement.



 
 





ek_shop_has_product
----------------------

Les liaisons shop-produit.


Les champs sont les suivants:

- shop: le shop
- product: le produit
- price: le prix de base pour ce shop
- wholesale_price: le prix d'achat pour ce shop
- quantity: la quantité disponible en stock. La valeur négative -1 est réservée pour les produits en quantité potentiellement illimitée (les produits virtuels).
Autrement, les valeurs négatives ne sont pas utilisées. 
- active: est-ce que ce produit est actif (1) ou pas (0)
- _discount_badge: ce champ est utilisé en interne par ekom pour accélerer le filtrage des listes de produits par réduction (20%, 30%, ...).
Vous ne devriez pas y toucher. 
- seller: le vendeur
- product type: le type de produit
- reference: la référence pour ce shop
- _popularity:  ce champ est utilisé en interne par ekom pour accélerer le tri par note de popularité. Vous ne devriez pas y toucher.
- codes: ce champ permet d'exécuter des actions spéciales. La syntaxe est la suivante: une série de composants séparés par une virgule.
        Les composants disponibles sont les suivants:
            - n: le produit est une nouveauté (le badge nouveauté décore le produit) 
- manufacturer: le fabricant
- ean: le code ean




Les liens-pivots sont les suivants:

- shop-product-providers ([ek_shop_has_product_has_provider](#ek_shop_has_product_has_provider)): les liaisons entre les "liaisons shop-produit" et les fournisseurs 
- shop-product-tags ([ek_shop_has_product_has_tag](#ek_shop_has_product_has_tag)): les liaisons entre les "liaisons shop-produit" et les tags
- shop-product langs ([ek_shop_has_product_lang](#ek_shop_has_product_lang)): les traductions des liaisons "shop-produit"




ek_shop_has_product_has_provider
-------------------

Les liaisons entre les "liaisons shop-produit" et les fournisseurs.


Les champs sont les suivants:

- provider: le fournisseur
- shop has product shop: le shop 
- shop has product product: le produit



ek_shop_has_product_card
--------------------

Les liaisons entre les shops et les cartes.

Les champs sont les suivants:

- shop: le shop
- product card: la carte
- product: le produit par défaut (celui qui s'affiche en premier sur la page de produits)
- tax group: le groupe de taxe utilisé
- active: est-ce que cette liaison est active (1) ou pas (0)



Les liens-pivots sont les suivants:

- shop-product card langs ([ek_shop_has_product_card_lang](#ek_shop_has_product_card_lang)): les traductions des liaisons entre les shops et les cartes  






ek_shop_has_product_card_lang
--------------------

Les traductions des liaisons entre les shops et les cartes.
Notez que les champs définis ici sont prioritaires (car plus spécifiques) sur les champs équivalents
définis dans la table [ek_product_card_lang](#ek_product_card_lang).

Les champs sont les suivants:

- shop: le shop
- product card: la carte
- lang: la langue
- label: le label de la carte pour le shop choisi
- slug: le morceau d'url identifiant cette carte pour ce shop
- description: la description
- meta_title: le meta title
- meta_description: le meta description
- meta_keywords: les meta keywords
    
    
    
ek_shop_has_product_has_tag
---------------------

Les liaisons entre les "liaisons shop-produit" et les tags, c'est à dire les liaisons
entre un shop, un produit et un tag.


Les champs sont les suivants:

- shop: le shop
- product: le produit
- tag: le tag



ek_shop_has_product_lang
--------------------

Les traductions des liaisons entre les shops et les produits.
Notez que les champs définis ici sont prioritaires (car plus spécifiques) sur les champs équivalents
définis dans la table [ek_product_lang](#ek_product_lang).

Les champs sont les suivants:

- shop: le shop
- product: le produit
- lang: la langue
- label: le label du produit pour le shop choisi
- description: la description
- out_of_stock_text: le texte à afficher si ce produit est en rupture de stock
- meta_title: le meta title
- meta_description: le meta description
- meta_keywords: les meta keywords
    



ek_tag
----------


Lorsque le client cherche un produit, il se peut qu'il ne connaisse pas l'orthographe
exacte du produit.

Par exemple, le client risque d'écrire "Kettle-bell" alors que dans
la base de données le produit s'appelle "kettle bell" (sans tiret).

Il serait dommage que le client ne trouve pas le produit à cause d'une erreur d'orthographe.

Les tags sont des étiquettes que l'on affecte aux produits et qui représentent des mots
permettant de trouver ces produits.

Ainsi, en ajoutant un tag "Kettle-bell" au produit "kettle bell", le client de l'exemple
précédent aurait trouvé le produit qu'il cherchait.

!> les tags ne sont pas sensibles à la casse.


Les champs sont les suivants:


- name: l'expression du tag pour la langue choisie
- lang: la langue


Les liens-pivots sont les suivants:

- shop-product-tags ([ek_shop_has_product_has_tag](#ek_shop_has_product_has_tag)): les liaisons entre les "liaisons shop-produit" et les tags



ek_tax_group
--------------

Les groupes de taxe.

Les champs sont les suivants:

- name: le nom symbolique (lettres, chiffres, underscore) du groupe de taxe
- label: le label représentant le groupe de taxe (la version humaine du nom symbolique)
- shop: le shop




Les liens-pivots sont les suivants:

- shop-product cards ([ek_shop_has_product_card](#ek_shop_has_product_card)): les liaisons entre les shops et les cartes
- tax group-taxes ([ek_tax_group_has_tax](#ek_tax_group_has_tax)): les liaisons entre "groupes de taxes" et les taxes





ek_tax_group_has_tax
-------------------

Les liaisons entre les groupes de taxes et les taxes.

Les champs sont les suivants:

- tax group: le groupe de taxe
- tax: la taxe
- order: l'order dans lequel sont associées les taxes
- mode: le mode de combinaison des taxes (si le groupe contient plusieurs taxes).
Les valeurs possibles sont:
    - (chaîne vide): même chose que chain
    - merge: ajoute les taxes entre elles avant de l'appliquer au produit.
Par exemple, pour une taxe A à 5% et une taxe B à 10%, c'est comme si on avait une taxe de 15% qui s'appliquait
    - chain: les taxes sont appliquées les unes à la suite des autres



ek_tax_lang
----------------------

Les traductions pour les taxes.

Les champs sont les suivants:

- tax: la taxe
- lang: la langue
- label: la traduction




ek_user
---------

Les utilisateurs (clients).


Les champs sont les suivants:

- shop: le shop
- email: l'email
- pass: le mot de passe encrypté
- pseudo: le pseudo
- first_name: le prénom
- last_name: le nom
- date_creation: la date de création
- mobile: le numéro de mobile
- phone: le numéro de téléphone fixe
- phone_prefix: le préfixe téléphonique
- newsletter: ce champ n'est pas utilisé. Utiliser la table [ek_newsletter](#ek_newsletter) à la place.
- gender: est-ce que la personne est un garçon (1), ou une fille (2)
- active_hash: une jeton crypté permettant de savoir si l'utilisateur a complété le processus d'inscription ou pas.
Lorsqu'une personne s'inscrit, le hash est créé dans cette table et envoyé simultanément au client.
Lorsque le client clique dans le mail, il prouve son identité à Ekom qui active alors son compte (champ active=1)  
- active: est-ce que le client est actif (1) ou pas (0). Un client inactif ne peut pas se connecter.



!> Note: le système d'activation des comptes dépend de la valeur de configuration `createAccountNeedValidation` du 
module Ekom.
Si cette valeur est à false, le client peut s'inscrire directement sans vérification par email, et le champ active_hash
devient alors obsolète.
Consultez la documentation développeur pour modifier la configuration du module Ekom.



Les liens-pivots sont les suivants:

- invoices ([ek_invoice](#ek_invoice)): les factures de ce client
- orders ([ek_order](#ek_order)): les commandes de ce client
- password recovery requests ([ek_password_recovery_request](#ek_password_recovery_request)): les demandes de renvoi de mot de passe liées à cet utilisateur
- product comments ([ek_product_comment](#ek_product_comment)): les commentaires de produit de cet utilisateur
- user-addresses ([ek_user_has_address](#ek_user_has_address)): les liaisons entre les clients et les adresses
- user-products ([ek_user_has_product](#ek_user_has_product)): les liaisons entre les clients et les produits
- user-user groups ([ek_user_has_user_group](#ek_user_has_user_group)): les liaisons entre les clients et les groupes de clients



ek_user_group
---------------

Les groupes de clients (aussi appelés groupes d'utilisateur).

Les champs sont les suivants:

- name: le nom symbolique (lettres, chiffres, underscore) du groupe d'utilisateurs (clients)
- shop: le shop



Les liens-pivots sont les suivants:

- user-user groups ([ek_user_has_user_group](#ek_user_has_user_group)): les liaisons entre les clients et les groupes de clients



ek_user_has_user_group
-----------------------

Les liaisons entre les clients et les groupes de clients.

Les champs sont les suivants: 

- user: le client
- user group: le groupe de clients






ek_user_has_address
----------------------


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




ek_user_has_product
----------------

Les produits favoris des clients.

!> Vous ne devriez pas supprimer des entrées de cette table.

Les champs sont les suivants:

- user: le client
- product: le produit
- product_details: les détails du produit (ne renseignez pas ce champ manuellement, il est rempli automatiquement par ekom)
- date: la date d'ajout dans la liste des favoris
- date_deleted: la date de suppression des favoris







Current:
ek_product