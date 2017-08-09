
About product box,
in the template **theme/lee/widgets/Ekom/Product/ProductBox/leaderfit.tpl.php**
we use the training-product module.


Note how this module RE-USES the attribute-selector selection mechanism of the regular product box
to its advantage: by providing just an uriAjax parameter, and hooking into two points:

- Ekom_prepareJsonService  (service/Ekom/json/api.php) 
- getProductBoxModelByCardId.$model  (class-modules/Ekom/Api/Layer/ProductLayer.php)
 
we can inject our module logic in the back end, and it's free in the front end.
 
 
I believe this "good practise" should be re-used in the future by other modules, in 
a similar use-case.