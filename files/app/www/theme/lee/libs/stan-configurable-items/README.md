Stan configurable items
===========================
2017-06-16




Help you implement a configurable items system in your application.


What's configurable items?
============================

Imagine a stack of horizontal items.

Each item has a unique id.

When you click on the item, it opens and the other items close.
When an item is open, you change configure it via a configuration panel.

Each item has its own configuration panel.

There is a submit button below the stack of items.
When you click the submit button, it returns you the item id and the configuration values for the selected item.
 
 
That's it. 



[![stan-configurable-items.jpg](https://i.postimg.cc/HWQKfnv5/stan-configurable-items.jpg)](https://postimg.cc/nMh0mFJc)



How does it work?
================



Right off the bat, if you learn by example, go directly to the "Code Example" section at the bottom of this page.


 
Init
------------
There is the **stanConfigurableItems** api, located in the **stan-configurable-items.js** file.

To instantiate the api, you need to call the **init** static method, like this:

```js
var jStanPayment = $(".stan-configurable-items-payment");
var oStanPayment = stanConfigurableItems.init("payment", jStanPayment);
```

In the above example, jStanPayment is the jQuery element that contains the whole
stanConfigurableItems gui that you want (you can have multiple stanConfigurableItems guis
on the same page if you want).

It's the context if you will, and lll the magic will happen inside this context.
The init method's first argument is the name of your api (you can have multiple apis running on the same page).
 
 
Modules
------------
 
Now before you actually call this init statement, you need to create your modules first.

A module is a piece of code that handles one or more stan items.

It's called a module because it should be created by your application modules.

Actually, that's the main concern of the stanConfigurableItems system: modules that don't know anything about
other modules can add their items in the centralized stan widget handled by the stanConfigurableItems api.

Modules should create a file, one per module, which makes a call to the **moduleAddCallback** method, like this:

```js
stanConfigurableItems.moduleAddCallback("payment", function (oApi) {});
```

To see a concrete example in action, open the **example-module1.js** file somewhere in this repository.
 
The first argument is the apiName. It has to be the same as the apiName that you've defined earlier with the **init** method (top example of this section).

The second argument is the initCallback; it receives the relevant **stanConfigurableItems** api instance (the "payment" instance in this example,
assuming we are creating a widget where the user can choose a payment method on an e-commerce checkout page).

Receiving the stan instance is the opportunity for the module to add the module handler to the api the like this:

```js
oApi.addHandler("MyModule1", new ModuleHandler());
```

A module handler is a callback that is responsible for returning the data of an item's panel.
A module handler can also be used for opening/closing the item's panel.

Here are the four methods that a module handler can have:

- init
- openPanel
- closePanel
- getData

Open a module file example for more details.


The first argument is the handler name, and the second argument is the handler instance.









  
Execute: the role of the different actors
------------

The **stanConfigurableItems** api instance takes care of opening/closing the selected item panels when you click on the items (see figure).

It will also handle their ids; this means when you call the **execute** method (which is the last method we're discussing here),
the ids passed to that method are RETRIEVED BY the **stanConfigurableItems** api instance.

However, the execute method also needs to be passed the panel options, and that is the responsibility of your module:
your module has (must have) a **getData** method which returns the options of the panel it handles.


So, what you should do is let the stan api handle your items, and add a submit button below the stack.

When the user clicks on this button, call the **execute** method, like this:


```js
oStanPayment.execute(function (id, options) {
    console.log("id", id);
    console.log("options", options);


}, function (errMsg) {
    console.log("stan error: ", errMsg);
});

```

The execute method takes two arguments: a success callback and an error callback.

In the background, the stan api will take care of calling the "getData" method of the module corresponding to the selected
item/panel.



The markup
--------------

I almost forgot the markup, without which none of what's explained above works (oops)!!

Here is the functional markup used by stanConfigurableItems:

```txt
- (stan context)
----- stan item:  .stan-configurable-item  .selected (when selected)   data-id="$n"
----- stan item panel:  .stan-configurable-item-panel  data-name="$moduleName"  (the name if the handler defined in your module code thanks to the api's addHandler method)
```

The "stan item panel" must be next to the "stan item".

Note: don't forget to set the **data-name** correctly otherwise it won't work (panels won't open).











Code example
===============

Here is the main code example.


```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="/theme/lee/libs/jquery/jquery-3.2.1.min.js"></script>
    <script src="/theme/lee/libs/stan-configurable-items/stan-configurable-items.js"></script>
    <script src="/theme/lee/libs/stan-configurable-items/example-module1.js"></script>
    <script src="/theme/lee/libs/stan-configurable-items/example-module2.js"></script>

    <style>

        html {
            box-sizing: border-box;
        }

        *, *:before, *:after {
            box-sizing: inherit;
        }

        .stan-configurable-items {
        }

        .stan-configurable-items .stan-configurable-item {
            border: 1px solid #ddd;
            background: #f5f5f5;
            padding: 10px;
        }

        .stan-configurable-items .stan-configurable-item.selected {
            background: #f3c7c7;
        }

        .stan-configurable-items .stan-configurable-item-panel {
            display: none;
        }


    </style>
</head>

<body>

<div class="stan-configurable-items">
    <div class="stan-configurable-item selected" data-id="1">Formatted item 1</div>
    <div class="stan-configurable-item-panel" data-name="MyModule1">
        Options for formatted item 1.
        <div class="panel">
            <label for="aaa">Is that what you want?</label> <input id="aaa" type="checkbox" value="1" checked>
        </div>
    </div>
    <div class="stan-configurable-item" data-id="2">Formatted item 2</div>
    <div class="stan-configurable-item-panel" data-name="MyModule2">
        Options for formatted item 2.
        <div class="panel">
            <input type="text" value="zoo">
            <select>
                <option value="1">France</option>
            </select>
        </div>
    </div>
    <div class="stan-configurable-item" data-id="3">Formatted item 3</div>
    <div class="stan-configurable-item-panel" data-name="MyModule3">
        Options for formatted item 3.
        <div class="panel">
            <label>
                Terminator <input type="radio" name="ali" value="1">
            </label>
            <label>
                Alibaba <input type="radio" name="ali" value="2">
            </label>
        </div>
    </div>
</div>


<div id="api-demo-buttons">
    <button class="validate">validate (check the console log messages)</button>
</div>


<script>


    $(document).ready(function () {
        //----------------------------------------
        // APPLICATION CODE
        //----------------------------------------
        var jStanConfigurableItems = $(".stan-configurable-items");
        var oMyStanApi = stanConfigurableItems.init("payment", jStanConfigurableItems);


        $('#api-demo-buttons').on('click', function (e) {
            var jTarget = $(e.target);
            if (jTarget.hasClass("validate")) {
                oMyStanApi.execute(function (id, options) {
                    console.log("do something useful with id: " + id + " and the options", options);
                }, function (err) {
                    console.log("oops", err);
                });
                return false;
            }
        });

    });
</script>
</body>
</html>








```


The **example-module1.js** and **example-module2.js** modules are also in this repository, have a look at their content.





History Log
------------------
    
- 1.2.0 -- 2017-06-16

    - multiple instances is now possible
        
- 1.1.0 -- 2017-06-16

    - now detect clicks INSIDE a stan configurable item
        
- 1.0.0 -- 2017-06-14

    - initial commit
    

