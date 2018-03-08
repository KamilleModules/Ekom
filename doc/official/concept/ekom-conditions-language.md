Le language des conditions Ekom
===========================
2018-03-08



Le language des conditions Ekom utilise une syntaxe de base inspirée des languages de programmation
traditionnels afin de faciliter son écriture.

Par exemple:

```txt
$date > 2018-03-08
```

La condition ci-dessus sera vraie seulement si la date est supérieure au 08 mars 2018.

Dans cet exemple, `$date` est appelée une variable de contexte.


Le contexte
----------------

Le contexte est l'objet qui contient l'ensemble des variables disponibles pour écrire les conditions.


Ce contexte varie en fonction de l'application (des modules installés) et de l'endroit d'où 
est appelée la condition.

Voici un exemple de context possible:

- date: 2018-03-08
- product_card_id: 56
- product_id: 56
- shop_id: 1
- lang_id: 1
- currency_id: 1
- ThisApp_isPro: true
- ThisApp_userShippingArea: FR
- ThisApp_userOriginArea: 0


Dans l'exemple ci-dessous, on devine que les 3 dernières variables sont apportées par le module `ThisApp`.







Les opérateurs arithmétiques
-----------------

Dans l'exemple précédent, nous avons utilisé l'opérateur arithmétique `>` (supérieur à) pour
écrire notre condition.

Les opérateurs suivants sont disponibles:

- `=` (égal à)
- `!=` (différent de)
- `<` (inférieur à)
- `<=` (inférieur ou égal à)
- `>` (supérieur à)
- `>=` (supérieur ou égal à)
- `><` (est compris entre, bornes exclues), cet opérateur attend deux opérandes séparées par une virgule
- `>=<` (est compris entre, bornes incluses), cet opérateur attend deux opérandes séparées par une virgule


Exemple: si la date doit être comprise entre le 10 et le 17 mars 2018 (inclus), on pourra écrire:

```txt
$date >=< 2018-03-10, 2018-03-17
```



Les opérateurs logiques
-----------------

Il est également possible de combiner plusieurs conditions entre elles afin de former une super condition.
Pour cela, on peut utiliser les opérateurs logiques suivant:


- `||` (ou)
- `&&` (et)
- `((` (commence un groupe logique)
- `))` (termine un groupe logique)


Par exemple, si on veut que la date soit inférieure au 10 mai 2018, et que l'utilisateur
soit un professionnel (en admettant que le contexte soit le contexte évoqué plus haut dans cette page),
on pourra écrire:

```txt
$date < 2018-05-10 && $ThisApp_isPro = true
```



Enfin, un dernier exemple un peu plus complexe (pour montrer les groupes logiques): si on veut que:

- soit: la date est inférieure au 10 mai 2018 ET l'utilisateur soit un professionnel
- soit: la date est supérieure au 02 mars 2018 ET l'utilisateur n'est pas un professionnel

on peut écrire:


```txt
(( $date < 2018-05-10 && $ThisApp_isPro = true )) || (( $date > 2018-03-02 && $ThisApp_isPro = false )) 
```










