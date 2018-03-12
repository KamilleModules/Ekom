Recettes  pour l'administration générée
==================
2018-03-10


Les recettes ci-dessous expliquent à l'utilisateur comment créer les éléments dans ekom en utilisant uniquement
[l'admin générée](user/back/generated-admin.md).


Créer un produit
-------------------



- Création de la carte dans le [catalogue général](concept/concept-base.md#le-catalogue-général) avec [ek_product_card](user/back/generated-admin-objects.md#ek_product_card)
- Création des chaînes traduisibles pour cette carte: [ek_product_card_lang](user/back/generated-admin-objects.md#ek_product_card_lang)
- Création du premier produit (catalogue général): [ek_product](user/back/generated-admin-objects.md#ek_product)
- Traduction de ce premier produit: [ek_product_lang](user/back/generated-admin-objects.md#ek_product_lang)
- Si le produit contient des attributs:
    - [ek_product_has_product_attribute](user/back/generated-admin-objects.md#ek_product_has_product_attribute)  
   - si les attributs n'existent pas, créez les avec:
        - [ek_product_attribute](user/back/generated-admin-objects.md#ek_product_attribute) et [ek_product_attribute_lang](user/back/generated-admin-objects.md#ek_product_attribute_lang) pour les attributs
        - [ek_product_attribute_value](user/back/generated-admin-objects.md#ek_product_attribute_value) et [ek_product_attribute_value_lang](user/back/generated-admin-objects.md#ek_product_attribute_value_lang) pour les valeurs d'attributs
- Association de la carte dans le shop désiré: [ek_shop_has_product_card](user/back/generated-admin-objects.md#ek_shop_has_product_card) et [ek_shop_has_product_card_lang](user/back/generated-admin-objects.md#ek_shop_has_product_card_lang)
- Association de tous les produits de cette carte dans le shop désiré: [ek_shop_has_product](user/back/generated-admin-objects.md#ek_shop_has_product) et [ek_shop_has_product_lang](user/back/generated-admin-objects.md#ek_shop_has_product_lang)

- Association de la carte aux catégories: [ek_category_has_product_card](user/back/generated-admin-objects.md#ek_category_has_product_card)
    - si les catégories n'existent pas, créez les avec: [ek_category](user/back/generated-admin-objects.md#ek_category)
- Si le produit a des caractéristiques:
    - [ek_product_has_feature](user/back/generated-admin-objects.md#ek_product_has_feature)
    - si les caractéristiques n'existent pas, créez les avec:
        - [ek_feature](user/back/generated-admin-objects.md#ek_feature) et [ek_feature_lang](user/back/generated-admin-objects.md#ek_feature_lang) pour les caractéristiques
        - [ek_feature_value](user/back/generated-admin-objects.md#ek_feature_value) et [ek_feature_value_lang](user/back/generated-admin-objects.md#ek_feature_value_lang) pour les valeurs de caractéristiques
                   
                   