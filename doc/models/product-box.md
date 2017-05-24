ProductBox model
===============
2017-05-23




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

Php example
----------------
```php



        $uri = "/theme/" . ApplicationParameters::get("theme");
        $names = [
            "balance-board.jpg",
            "balance-board-logo.jpg",
            "balance-board-demo.jpg",
            "balance-board-arriere.jpg",
            "balance-board.jpg",
            "balance-board-logo.jpg",
            "balance-board-demo.jpg",
            "balance-board-arriere.jpg",
        ];


        /**
         * The keys of the images are fileNames (like "balance-board.jpg" for instance)
         */
        $images = [];
        foreach ($names as $fileName) {
            $images[$fileName] = [
                'thumb' => $uri . "/img/products/balance-board/thumb/$fileName",
                'small' => $uri . "/img/products/balance-board/small/$fileName",
                'large' => $uri . "/img/products/balance-board/large/$fileName",
            ];
        }

        $boxConf = [
            "images" => $images,
            "defaultImage" => "balance-board.jpg",
            "label" => "Balance Board",
            "ref" => "1436",
            "description" => "Plateau de freeman en bois idéal pour travailler les muscles stabilisateurs, l'équilibre et la coordination. Ultra résistant grâce à son bois robuste, le plateau dispose d'une surface antidérapante.",
            /**
             * Is used by the widget to assign visual cues (for instance success color) to the stockText
             * List of available types will be defined later.
             */
            "stockType" => "stockAvailable",
            "stockText" => "En stock",
            "price" => "12.69 €", // note that price includes currency (and relevant formatting)
            // if type is null, the price is not discounted,
            // otherwise, the discount_ data help displaying the right discounted price
            "discount_type" => null,
            "discount_amount" => "0",
            "discount_price" => "0",
            "attributes" => [
                'weight' => [
                    "label" => "poids",
                    "values" => [
                        ["0.5 kg", ""],
                        ["1 kg", "selected"],
                        ["2 kg", ""],
                        ["3 kg", "disabled"],
                        ["4 kg", "outOfStock"],
                        ["5 kg", ""],
                    ],
                ],
            ],
            //--------------------------------------------
            // EXTENSION: SPECIFIC TO SOME PLUGINS
            // consider using namespace_varName notation
            //--------------------------------------------
            // rating
            "rating_amount" => "80", // percent
            "rating_nbVotes" => "6",
            // video
            "video_sources" => [
                "/video/Larz Rocking Leaderfit Paris 2017 Step V2.mp4" => "video/mp4",
            ],
        ];

        return $boxConf;
```


