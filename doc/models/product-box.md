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
- product_id: the product id 
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
  
  
- hasDiscount: bool, whether or not this product has at least one discount applied to it.
- originalPrice: string, the original price to display, either WT or OT, based on ekom preferences/rules                        
- salePrice: string, the discounted price to display, either WT or OT, based on ekom preferences/rules
      
- savingPercent: negative percent (-10% for instance)
- savingAmount: negative formatted price (-7€ for instance)
            

- badgeDetails: array of badgeDetail, each badgeDetail is an array with the following structure:
    - type: amount|percent
    - value: number if percent, or formatted negative price otherwise
    - label: string, the discount label
    
      
- taxDetails: array of items, each item having the following structure:
    - amount: the percentage applied for that node (which in case of merged taxed is the sum of all taxes)
    - labels: an array of labels of taxes used for that node
    - ids: an array of ids of taxes used for that node
    - groupLabel: the label (bo label) of the tax group
    - priceBefore: the raw price before entering the node
    - priceAfter: the raw price after exiting the node
    
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




What's the fuss about the prices, why so many prices?
==========================================

You might be frightened by all the variation of price and discount related entries in this model.
You might even wonder if that's really necessary.

In this section, I explain the original vision, so that you can use it and see if it works for you.


The main idea is to give all info to the template author and let them decide exactly what they want to display.
That's because a price can be displayed in various way, each variation being legitimate.

Plus, we (the ekom system) give template authors our recommendation about how the price should be displayed (based
on ekom configuration), but we always provide all the tools for the template authors to override this logic,
this explains partially why we have so many keys.
 
 
 
withTax/withoutTax
--------------------
Then you have the withTax/withoutTax criteria.

On a product box, a price without discount is often displayed with taxes or without taxes, depending
on the shop business mode (b2b and b2c seem to be the major modes).


displayPrice
--------------------

In ekom, we let the user decide how price should be displayed on the product box with an option (see the ekom doc 
for more info).

Therefore, depending on the configuration, the price to display might be either the price with tax or the price without tax.

To save the template author from choosing (because the less business choices a template author has the better) 
which price to display, we provide the displayPrice, which is either the priceWithTax or the priceWithoutTax, 
depending on ekom config.


discount
-------------

So far we've talked about the following price types already:

- priceWithTax
- priceWithoutTax
- displayPrice

...and their unformatted equivalent.


Then throw the discount into the mix.
Before we do so, let's talk a bit about how a discount price is displayed on a product box.

Some nomenclature might help.

A price with discount always has the discount word in it (in ekom).
    
A price with discount can be displayed with two more bits of information:
the badge and the old price:

- discountPrice - oldPrice - badge
    
Template authors decide exactly what they want to display
(for instance, a template author will display only the discountPrice and the badge, 
while another template author will display all three elements).
    
    
    
In ekom, we decided that the oldPrice was the price without discount, so, one of:
    
- priceWithTax
- priceWithoutTax
- displayPrice    
    
And to differentiate the discount price from those price, we add the discount suffix to our price types,
this gives us those new price types:

- priceWithTaxDiscount
- priceWithoutTaxDiscount
- displayPriceDiscount


So, if you can follow this logic, if a template wants to display a price with this format:

- discountPrice - oldPrice - badge
    
Then she can use the following price types for instance:
    
- displayPriceDiscount - displayPrice - badge
    
    
Badge
----------
I forgot to talk about the badge.
The badge is often expressed as a percentage or an amount.

The keys controlling the badge are the following:

- discount_type
- discount_operand
- discount_price

The type (discount_type) should be used as a trigger (by template authors) to know what type of badge 
to display (a percentage, an amount, or something else).



 
    
hasDiscount
--------------
Knowing whether or not the product has a discount is the first bit of information template authors will need,
and so we decided to provide it as a variable: hasDiscount.

Note that this is the only bit of logic that we provide to templates.
All other bits are dumb by nature.


