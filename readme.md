# RecipeLang

Concept inspired by CookLang.
Recipes are text files with fields put directly into each step, in a pre-determined format.  Recipe files are stored in a directory name `rcp` and files within also have the `.rcp` extension.

## Meta Data

Meta data fields can be pretty much anything you like.  Fields are denoted with the `@` character and are limited to a single word followed by a tab and then the information you want to include.  The following fields are some examples:

The following are the only required fields for the meta data section.

`@name  Hash Browns` The name of this recipe is Hash Browns

`@desc  Better than store bought` The description of the recipe

`@category  breakfast, snack` This falls into the breakfast and snack categories

`@yields  4 servings` This recipe makes four servings

When you have finished adding your meta data, separate it from the recipe with 2 blank lines

## Writing out your recipe

Each element is delineated using the pipe `|` character

- ingredients are enclosed in curly braces `{}`
  - ingredients can be just a name, name and measurement, name and method, or all three
  - items made up of multiple ingredients can be included directly within the ingredient (see complex recipe)
    - `@{carrot|2-3|diced}` We need 2 to 3 carrots that are diced
    - `@{steak|cut in strips}` Cut the steak into strips
    - `@{bouquet|@{bay|1 sprig} @{thyme|1 sprig} @{parsley|1 sprig} tied in cheesecloth}` Would denote that the bouquet ingredient is made up of bay, thyme and parsley, and these ingredients will be added to the recipes ingredient list
- cookware/utensils are enclosed in square braces `[]`
  - `@[large non-stick frypan]` add a large non-stick frypan to the cookware required list
  - `@[pork dolly|jam jar]` add a pork dolly to list and place 'jam jar' after it in brackets
- timers for a particular steps enclosed in rounded braces `()`
  - `@(30 minutes|cook)` will have the time inserted into recipe, and added to the cook timer
  - `@(prep|30)` will only add 30 minutes to the prep timer ( time is in minutes )
- includes are marked up as follows
  - `~"Pastry Recipe"` this would insert the 'Pastry Recipe' at the point in the recipe it is found

If you want your recipe ingredients broken down by sections, use the `#` symbol followed by a name.  If you do use named sections, add a blank line after the steps for the section.

General timers can be added to section titles, and they will not be included within the displayed recipe, but rather get added to the overall prep/cook timers.

## Basic example

```
@name	Easy Crepes
@desc	Take this versatile crêpe recipe in a sweet or savoury direction, depending on what you fancy.


@(prep|20) @(cook|15)
Crack the @{eggs|3 large} into a @[blender], then add the @{flour|125 g}, @{milk|250 ml} and @{sea salt|1 pinch} and blend until smooth.
Pour into a @[bowl] and let stand for @(15 minutes|prep) to thicken
Melt some @{butter|for frying} in a @[large non-stick fry pan] on a medium heat, then tilt the pan so the butter coats the surface.
Pour in 1 @[ladle] of batter and tilt again, so that the batter spreads all over the base, then cook for @(1 to 2 minutes|cook), or until it starts to come away from the sides.
Once golden underneath, flip the pancake over and cook for 1 further minute, or until cooked through.
Serve straightaway with your favourite topping such as @{strawberries|sliced}.
```

## Complex example

```
@name	Melton Mowbray pork pie
@desc	Have a crack at making the gold standard of pork pies - it's surprisingly easy and very, very impressive.
@yield	Serves 4-6
@prep	over 2 hours
@cook	over 2 hours
@note	The pastry is best made the day before and kept in the fridge wrapped in cling film. Bring to room temperature before rolling out.


# Pastry @(prep|20) @(cook|20)
For the pastry, place the @{lard|150 g}, @{milk|50 ml} and @{water|50 ml} into a @[small pan] and gently heat until the lard has melted.
Sift the @{flour|450 g} into a @[large bowl] and season with @{salt & pepper} and mix well
Make a well in the flour and pour in the warm lard mixture, mixing until it comes together to form a dough. Knead for a few minutes, then form into a ball and set aside. Prepare the @{eggs|2 large|beaten} wash.

# Pork Jelly @(prep|30) @(cook|210)
For the pork jelly, combine @{pork bones|900 g}, @{pig's feet|2 each}, @{carrots|2 large|chopped}, @{onion|1 large|peeled & chopped}, @{celery|2 stalks|chopped}, @{bouquet|1|using the @{bay|2 leaves}, @{thyme|1 sprig}, @{parsley|1/2 bunch}} (wrapped in cheesecloth, tied with string) and @{black peppercorn|1/2 tbsp} into a @[large pan] and pour in enough water to just cover.
Bring slowly to the boil, then reduce the heat to a simmer, and cook for 3 hours over a low heat, skimming off any scum that rises to the surface
Strain the stock through a @[fine sieve] into a clean @[sauce pan] and discard the solids, simmer over a medium heat until the liquid has reduced to approximately 500ml/1 pint.

# Pork Filling @(prep|20) @(cook|60)
For the filling, place the @{pork shoulder|400 g|finely chopped}, @{pork belly|55 g|skin removed, minced}, @{lean bacon|55 g|finely chopped}, @{allspice|1/2 tsp} and @{ground nutmeg|1/2 tsp} into a @[large bowl] and mix well with your hands. Season with @{salt & pepper|to taste}.
Preheat the oven to 180C/350F/Gas 4
Line a @[pork pie dolly|or jam jar] with @[cling film] to prevent the pastry from sticking.
Pinch off a quarter of the pastry and set aside. On a floured work surface, roll out the remaining three-quarters of pastry into a round disc about 3cm/1.25in thick. Place the pie dolly into the middle of the pastry circle and draw the edges of the pastry up around the sides of the dolly to create the pie casing. Carefully remove the dolly from the pastry once your pie casing is formed.
Roll the pork pie filling into a ball and carefully place into the bottom of the pastry case. Roll out the remaining piece of pastry into a circle large enough to cover the pastry case as a lid. Brush the top inner parts of the pastry casing with some of the egg and place the pastry circle on top. Pinch the edges of the pastry to seal the pie. Brush the top of the pie with the rest of the egg, then bake in the oven until the pie is golden-brown all over.
Remove the pie from the oven and set aside to cool.
Cut two small holes in the top of the pork pie and pour in the pork jelly mixture (you may need to heat it through gently to loosen the mixture for pouring). Chill in the fridge until the jelly is set.```
```


### Notes

This is a partially complete project, sections for displaying recipes, shopping lists and searching recipes have still to be completed.
Indexing and search features are also planned.

