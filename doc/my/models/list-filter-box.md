ListFilter box
============
2017-06-24



A list filter box is a little widget on the side of a list, helping filtering the list.

Each box is basically a list of links.

When the user clicks a link inside the box, it filters the list.
 
Visually, it might looks like this:
 
 
```txt
- color
----- green 
----- red 
----- blue

- size
----- S 
----- M 
----- L 
``` 



The model is the following:


```txt
- filterBoxes:
----- $name:        (the filter symbolic name)
--------- type: the type of the filter box
                    - items:
                    - minMax

--------- title: the suggested label for the box

(only for filter box of type items)
--------- items: 
------------- $index:
----------------- label: label of the value to display
----------------- uri: the uri to use to add THIS attribute value to the current list filters  
----------------- selected: bool, whether or not the filter is selected
                                Note: templates can remove the selected filters if they know 
                                that those selected filters are centralized in another widget
                                like summary filters.
                                
(only for filter box of type minMax)
--------- minValue: number, the minimum value 
--------- maxValue: number, the maximum value 
```
