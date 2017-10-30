Ekom various concepts
========================
2017-10-28




User current shipping address
------------------------------
The current shipping address is the information about the current shipping address
being used by the user.

By default, it's the default address from the database (ek_user_has_address.is_default_shipping_address).

But, as soon as the user starts the checkout process and reaches the shipping step, 
then the current shipping address is the one that the user selected.

Note: when the checkout process completes, the checkout data vanishes and the current shipping address
is the default address from the database again.

 
 
User country
------------------------------
To know the user's country is sometimes useful.
In ekom, we provide this variable as a part of the context of the box model (see the **ekom-product-box-model.md**
document for more info).

However, the implementation is not perfect, but I added a workaround to make it at least open to new ideas.
It works like this:


- we first try to get the country from a dedicated session variable: userCountry
        use the EkomSession::get method to access it.
        (This was my work around, it allows us to implement any logic we want)
        
- if this variable is not set

    (STARTOF---if we believe that we should use the billing address)
            
    - we first try to get the country out of a billing address:
    - if the user has completed the checkout's shipping step, then use the billing address that
            the user chose 
          
    - else if the user is connected, we look at her/his addresses: 
        - if the user has a billing address, then we use the country of this billing address
        - else if the user has no billing address but has at least one address, we take the first address 
                    found (the one with the lowest order) and extract the country from it
    (ENDOF---if we believe that we should use the billing address)
    
    - if the user has no address or if she/he is not connected, then we use the browser's provided country
                
                    
The source code is in: **UserLayer.getCurrentCountry()**.



Now we can see the limitations of this algorithm: it thinks that the user's country (i.e. where she/he lives)
is the same as the billing address.
In real life, this assertion is not always true.
But now, thanks to the userCountry variable, we can ask the user: where do you live directly?
Perhaps during the checkout process, or perhaps as a popup on the home page, or perhaps
on a product box?                    