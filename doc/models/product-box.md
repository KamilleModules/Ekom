ProductBox model
===============
2017-05-23 --> 2017-05-26




This model can take multiple forms:

- the normal form represents the box model as expected
- an error form represents the box model when something wrong happened (for instance,
        the product card wasn't found)
        

The benefit of using one hybrid model over a controller dispatching multiple models,
beside the semantic discussion, is that we can cache the result of the model
in its relevant form.

In other words, we can cache more with this technique.


Now, for the sake of the semantic discussion:

we basically ask a template to do multiple things at once, or to be multi-states (depends
how you see it), which is arguably a bad thing (because do just one thing but do it well is a good thing, right?).
However, what we are asking is really to display simple error messages, nothing fancy,
and arguably, an error message belongs to the widget it's attached to, not ANOTHER view.

You make your own opinion on it, I choose that it's okay to pass such hybrid models 
as long as the other forms are just error messages.

Now if you think about this, and if you agree with my point of view, then
we might improve on that and say that this is a pattern.





The error form looks like this:

- errorCode: a code indicating the type of error, the code can be a string like "unavailable", or anything else 
- ?errorTitle: an error title 
- ?errorMessage: an error message
 
If the errorCode key exists in the model, then it means that the model
is in the error form; otherwise it's in the normal form.

 
(We could re-use those error keys whenever any widget is in erroneous mode? just suggesting the idea here) 





The normal form is presented below:        

```txt

- images: array of 
                $fileName => \[
                    thumb => $uriImageThumb, 
                    small => $uriImageSmall, 
                    medium => $uriImageMedium, 
                    large => $uriImageLarge, 
                ]
- defaultImage: $fileName of the default image (key of the images array)
- label: string
- ref: string
- description: string
- stockType: string representing info about the stock, possible values are:
                - stockAvailable: the stock is available
                - outOfStock: the stock is not available, because quantity is 0
                
                The template should use this value to format the stockText (using
                different colors for instance).
                
- stockText: string, the text to display
- price: string, the formatted price
- discount_type:
- discount_amount:
- discount_price:
- attributes: array of $attrName => $attrInfo.
                
                Should be used to display an attribute selector subwidget.
                
                The attribute selector might look as a stack of attribute lines,
                each lines containing the possible attribute values in form of clickable/grayed out buttons.
                Something like this for instance:
                
                 * ---------------
                 * Color:
                 *      red    <green>    blue
                 * Size:
                 *      4   6   <8>   10
                 * ---------------                
                
                
                The $attrInfo is an array with the following structure:
                - label: string, the label of the attribute
                - values: array of $attrValueInfo, each of which being an array with 
                                        the following structure:
                                        - value: string, the value of the attribute
                                        - selected: 0|1, whether this attribute value is the one selected
                                        - active: 0|1, if 0, it means the product has been de-activated by the shop owner (ek_shop_has_product.active), 
                                                        and the user shouldn't be able to select this value 
                                        - quantity: number, the quantity of products if that value was chosen
                                        - existence: 0|1, whether or not the product for this attribute value does exist.
                                                         Read Module\Ekom\Utils\AttributeSelectorHelper's source code top comment for more info.
                                        - productUri: string, the uri to the corresponding product.
                                        - getProductInfoAjaxUri: string, the uri to the ajax service to call to get information
                                                                about the product
                                        - product_id: string, the product id
                                                                
                                                                        
                                                                    



// extensions

- rating_amount:
- rating_nbVotes:
- video_sources:

```