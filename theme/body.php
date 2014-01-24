<canvas class="phy_tree"><img class="phy_tree" src="/theme/fractals/pyth_tree.php" /></canvas>
<header class="inline">
<?php
/*
Here I want
-Similar to ubuntu, The icons are on the left side
Scroll through the icons
-On click-The icon is high lighted jand bounces, a larger icon appears on top left hand corner and page opens
-On hover-Shows tool tip and highlights in a different color


I'm running into an issue...
I don't want to focus on applications but rather I'd like to focus on 
The overall flow of the application

For example

for views I need
-Mobile and desktop seperated

At the same time I need to get rid of how everything is based of ajax

Also I the only things that are decent is the passthrough in terms of routing
If I'm making a call to a data object I need to
-Check Permissions
-Give them the object as displayed by 
	-theme choice
	-plugin
	-something generic that gives generic controls

When displaying an item how do I make it look decent without having to create a custom view for each one?
Or even making the custom view easy?

Perhaps I'm waisting too much time on "ideas" and not enough on action
tools are used to make action easier and ideas created


*/?>
<nav id="menu-main">
<h2 style="color:#FFF">Applications</h2>
<ul class="vertical"><?php

include_once __ROOT__."Core/App/functions.php";

$app_array = getAppList();

foreach($app_array as $app){
?><li>
<a href="<?php echo $app["init_url"]; ?>" >
	<img src="<?php echo $app["pic"]; ?>" /><br />
	<span class="menu-title"><?php echo $app["title"]; ?></span>
</a>
</li><?php
}
?></ul>
<h2 style="color:#FFF">Content</h2>
<ul class="vertical"><?php

include_once __ROOT__."Core/Data/children_detect.php";

$decent = Extended_Object::find(array(),20);

foreach($decent as $c){
if($c->default_app == "none") continue;
include_once __ROOT__.$c->location;
?><li>
<a href="/<?php echo $c->classname; ?>.html" >
	<img src="<?php echo $app["pic"]; ?>" /><br />
	<span class="menu-title"><?php echo $c->classname; ?></span>
</a>
</li><?php
}
?>
</ul>
</nav>
</header><section class="inline psuedo_frame">

</section>