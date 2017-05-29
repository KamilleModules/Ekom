Config
==================
2017-05-27


As a kamille module, ekom inherits the default configuration system of a kamille module.

However, since ekom uses the concept of shops, and each shop can have its own configuration,
we need a more advanced system.


Ekom's config is composed of multiple layers:

- the kamille module config
- the database
- the ekom web context
- the ekom contextual config


For kamille module's config, please refer to the kamille module documentation.
 
 
Database
------------
The database was the first storage available to the ekom system.
It's rudimentary, but functional.

Ekom stores the shop information in two tables:

- ek_shop
- ek_shop_configuration


Both tables define general configuration for the shop, but the ek_shop_configuration table
is open to new values; it is meant to be used by OTHER kamille modules serving as ekom plugins (sort of).


But this static configuration doesn't handle all the cases ekom had to deal with.

Imagine that we want to create a configuration option defining whether to display the price with or without taxes on 
the front office.

This might look at first pretty straightforward: just create a display_price_with_tax option and set the value to 
either 0 or 1, right?

Well, there is one more constraint though: we wanted this value to depend on whether the customer is b2b or b2c.

Now, we start to see that the database static storage is maybe not the best storage for that kind of 
conditional configuration values.

We could have forced it into the database, by creating a special conditional notation, but there had to be a better way.

The ekom contextual config offers a cleaner solution for that problem.




Ekom web context
---------------------
This configuration is created by the EkomApi.
It provides some basic values that the front can use:


- ekom.host
- ekom.shop_id
- ekom.lang_id
- ekom.lang_iso
- ekom.currency_id
- ekom.currency_iso
- ekom.currency_rate


Ekom contextual config
-------------------------

Ekom contextual config is a file based configuration for the front office ().
And to be more precise, a php file based one.

This means you can use the php language to determine the values of the configuration, which basically means: you can do anything.

It's named contextual because this type of configuration is always dependent on an ekom entity (be it a shop, or the combination
of a shop and a currency for instance).

Note: if it wasn't contextual, we could simply have used the kamille module's default configuration system.

The ekom contextual config is accessible via the E utility provided by ekom.

We chose to do so because it would give us the possibility to pass the configuration to the other modules (via kamille hooks)
before returning the value.

So if we take our display_price_with_tax option previous example back, our synopsis would basically be the following:
as the developer, you just ask for the value using the E::conf($key) method.

Now what happens under the hood?

The E class will load the configuration in memory, using the **app/config/Ekom** files (unless it's already loaded),
and then will allow other modules to update it once per "php process", and give your value back.

Now you might wonder: what if a module wants to return a super dynamic value (a value that could be different for the same "php process",
like a random value for instance)?
   
Well, we consider this an edge case for now, and nothing is implemented for this, BUT, we since the config access is encapsulated
in E::conf, we would just need to add another type of hook (a super dynamic hook) just before returning the value.



Ekom contextual config is only available once the web context has been initialized (use EkomApi.initWebContext method).

The configuration keys are the following (you can access host or host-currency levels independently):



### host level
- displayPriceWithTax: bool=true, whether to display the prices with taxes applied or without taxes applied on the front office.


### host-currency level
- moneyFormatArgs: array, display price preferences, has the following keys: 
        alwaysShowDecimals: bool=true,
        nbDecimals: int=2,
        decPoint: string=".",
        thousandSep: string="",
        moneySymbol: string="€",
        moneyFormat: string="vs", the end format of the price.
                                The "v" letter will be replaced by the value of the price.
                                The "s" letter will be replaced by the symbol of the currency.
                                


 


