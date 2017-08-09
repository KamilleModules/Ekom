Price rules
==============
2017-05-29




One of the most important function in ekom is the one that displays a price.


When displaying a price, different elements come into play:

- shipping costs
- tax rules
- price
- discounts



Prices are also displayed at different places, which can be organized in two groups:

- the front, including:
    - the product box
    - the product list
    - the product mini-cart
    - the product cart

- the order, including:
    - the pdf order that a user can download
    - the checkout page


Basically, the front prices and the order both display information about the product(s) being purchased.

The order form is a more complete/detailed form, and the front prices only display a subset of those information.
 
 
I searched the web for a magic formulae which would apply to any e-commerce.
Maybe I'm not an efficient googler, but didn't found such a formulae: basically there is always a guy complaining
about what an e-commerce solution cannot do.
Oops.

Still, we need to move forward, and here is my approach.

A common tendency I found is to differentiate price_without_taxes from price_with_taxes.
Usually, b2b express their prices without taxes, while b2c express their prices with taxes.

Now the "usually" is exactly the problem, since we try to make a solution that would adapt any need.

Unfortunately, I don't know much about how every taxes in every country work, so, rather than trying to provide
useful options for specific countries, I decided to let the user configure her shop, while I will try to provide the 
tools to do so.


My first problem is the taxes.
To make things simpler for me, I will consider taxes as ONE ATOMIC block, so that we can either apply taxes,
or not apply them, but if we have a product with 3 taxes A, B, C, and those taxes don't apply as a bloc but
rather applied at different phases, depending on X conditions, then we will need to hack the system.

So my biggest fear (or joy?) right now is that someone brings an use case where a conditional split of taxes
is required for computing the price.
Ok, let's continue though.


Here is the basic logic I came up with, first for the front prices:

- price without tax
- (pause)
- price with tax
- (pause)


The pauses are the places where the discount can kick in.
When you set a product discount, you always specify the target: either the price without tax or the price with tax.

- display price 
- display price quantity 
- quantity 

display price is the price that we display on the front: it's either the original price, or the price with tax.
The display price exist for templates, so that a regular template know exactly the price to display (the ekom module takes
care of selecting the right value to display, based on user preferences or other heuristics).

Note: it's worth (at least for me) keeping in mind that it's always possible for a template to decide 
to not follow the ekom logic, and to not display the display_price, but rather any price it wants.
In fact, ekom models always contain all the prices information, to allow this override.
(Sounds weird, but this gives more flexibility to the system, at the risk of sounding as if the rules were
broken by definition).


The display price could also be the discounted version of the price.
Generally though, we like to display the discount next to the price, to entice the user to buy the product (making her feel
comfortable about how much she would save if she buys the product now). 

Therefore, the discount is given separately:

- discount_amount:
- discount_type: percent|fixed
- discount_price: the discounted price


The display price quantity is the display price multiplied by the quantity of items in the cart,
it's the price that we display in the cart and sometimes in the minicart.

Note: some discounts might apply only to one product, so that if you order 4 products or the same type (for instance),
the discount only applies to the first product.
Ekom takes care of those kind of problems internally and return a display price quantity that take all that into account.
After a second thought, for this edge case, I believe, we can always workaround by creating discounts that don't create
those kind of problems (but yet achieving the same results): for instance for this edge case we could create a discount
that applies only once (and so the discount applies naturally on all products, it's just that the user cannot cumulate
two of them).
My point is that in the end the display price quantity is REALLY EXACTLY the display price multiplied by the quantity:
it doesn't get any more complicated than that.


Quantity is the number of products of a certain type in the cart.


So to recap, those are the information that we need to display a front price:

- price_without_tax
- price_with_tax
- discount_amount:
- discount_type: percent|fixed
- discount_price: the discounted price
- display_price
- display_price_quantity
- quantity




For the order, we use the ekom order model (see the ekom order model schema in this repository).
Basically, the same idea is implemented, but the name are different (and also we put shipping into the mix).



Order model
--------------


Here is how the order is organized in ekom.

See schema.

From the information below we can recreate an user order.



Order lines are grouped by carrier, since an order might involve more than one carrier method (and the shipping split into chunks).


We start at the line level.

Each line has a number of info available.
One will probably not use them all, but only a subset.

The available fields are the following:

- reference
- product: the name of the product 
- description: an accurate (contains all attributes info) description of a product reference 
- quantity: how many of this reference have been ordered 
- weight: the weight for one unit of the given reference 
- baseUnitPrice: the price for one unit of the given reference (the raw price, as defined in the backoffice, with nothing applied to it)  
- basePrice: the baseUnitPrice multiplied by the quantity  
- preTaxDiscount: a list of discount labels applied to the basePrice, before the taxes (if any) are applied.

        A discount label shows a human readable summary of all relevant information about a (line/product) discount.
        All those bit of information are still available as separated fields.
        
        A discount is ultimately applied to the product, and can only take one of two forms:
            - a fixed amount discount
            - a percent amount discount
        
        We describe the process of updating the price as applying a discount technique to a price.
        
        The available bit of information are provided as items of the preTaxDiscountItems list.
         
        The preTaxDiscountTechnique and preTaxDiscountTechnique keys (of each item) are all we need to apply the discount.
        The preTaxDiscountLabel field is more of a human reminder.
                    
- preTaxDiscountItems: an array of item, each of which containing the following entries:     
    - preTaxDiscountTechnique: fixed | percent         
    - preTaxDiscountAmount: see preTaxDiscount field        
    - preTaxDiscountValue: see preTaxDiscount field
    
- price: BasePrice x PreTaxDiscount
- tax: a list of tax rules labels applied to the relevant product reference.

            A tax rule label shows an human summary of a tax rule.
             
            A tax rule always applies a percent amount to the price,
            and this percent amount is given as the taxPercent field.
            
            All tax rules labels can be accessed in greater details individually via the taxItems key described hereafter.
            
            When a product has multiple taxes, we can define how those taxes are applied with the
            line.tvaOperator value of an ekom order model, which refernce might be available
            via the shop table (when it's implemented, today is just brainstorming).
            
            
            
- taxItems: an array of item, each of which containing the following entries:
    - taxPercent: the amount of percent of a given tax       
    - taxLabel: the label of the given tax, as defined in the backoffice       

- priceWithTax: the Price with the taxes applied 
- postTaxDiscount: same as preTaxDiscount, but applied on the PriceWithTax
- postTaxDiscountItems: same as preTaxDiscountItems, but for postTaxDiscount     
- totalLine: PriceWithTax with postTaxDiscounts applied 
       
       
       
So all lines are grouped in a so called CarrierGroup, which is just a container for those lines.
The CarrierGroup also brings its own fields into the mix:
       
- basePriceSubtotal: the sum of the "basePrice" column of all lines in this group        
- priceSubtotal: the sum of the "price" column of all lines in this group        
- priceWithTaxSubtotal: the sum of the "priceWithTax" column of all lines in this group        
- totalLineSubtotal: the sum of the "totalLine" column of all lines in this group        
- carrierInfo: a carrier summary of info related to the carrier      
- carrierLabel: the label of the carrier        
- carrierTechnique: fixed | percent, same principle as a preTaxDiscountItems item        
- carrierAmount: 
- priceCarrierGroupSubtotal: the priceSubtotal value, with carrier pricing strategy applied to it 
- priceWithTaxCarrierGroupSubtotal: the priceWithTaxSubtotal value, with carrier pricing strategy applied to it 
- totalLineCarrierGroupSubtotal: the totalLineSubtotal value, with carrier pricing strategy applied to it 
- totalWeight: the sum of all weights (multiplied by the relevant quantities) 
- carrierGroupSubtotal: this field is a little different,
                        it takes the value of one of the following keys:
                            - priceCarrierGroupSubtotal
                            - priceWithTaxCarrierGroupSubtotal
                            - totalLineCarrierGroupSubtotal
                            
                        The goal is that the value selected will be used to compute the grand total of the order.                            
                        The choice depends on the carrierSubtotalTarget key, which might find its way to the shop
                        table at the time of implementation.
- lineItems: list of all line items, each of which containing the array described previously
       
       
Now it's time for the grand finale.       
All those groups are mixed together in a total section, which contains the following fields:

- basePriceTotal: sum of the "basePriceSubtotal" columns of every CarrierGroup
- priceTotal: sum of the "priceSubtotal" columns of every CarrierGroup
- priceWithTaxTotal: sum of the "priceWithTaxSubtotal" columns of every CarrierGroup
- totalLineTotal: sum of the "totalLineSubtotal" columns of every CarrierGroup
- priceCarrierGroupTotal: sum of the "priceCarrierGroupSubtotal" columns of every CarrierGroup
- priceWithTaxCarrierGroupTotal: sum of the "priceWithTaxCarrierGroupSubtotal" columns of every CarrierGroup
- totalLineCarrierGroupTotal: sum of the "totalLineCarrierGroupSubtotal" columns of every CarrierGroup
- totalWeight: sum of the "totalWeight" columns of every CarrierGroup
- carrierGroupTotal: sum of the "carrierGroupSubtotal" columns of every CarrierGroup
- grandTotal: alias for the carrierGroupTotal field
- carrierGroupItems: list of all CarrierGroup items, each of which containing the array described previously
       
       
So, name this big array ekomOrderModel, and voilà! You've got an useful array at your disposal. 
       
       
       
Notes: 
- whether a preTaxDiscount or postTaxDiscount is applied depends on the discount condition/actions set in the backoffice.
       We generally use preTaxDiscount only.
        
- note that a tax can be applied after or before the product is multiplied by the quantity, without affecting the business,
        thanks to the multiplication properties:
        
                    (1 x 10) x 10/100 = (1 x 10/100) x 10
















