<?php 
include "../../genericfunk.php";
$url = getURL(__file__);
?>

<div id="rc" style="width:500px;height:500px;">
	
</div>

<script type="text/javascript" src="<?php echo $url; ?>/game.js"></script>
<script type="text/javascript">
jQuery(function($){
	var gameob = new game();
	var start = $("<a href=\"#\" style=\"font-size:30px\">Start</a>");
	start.click(function(e){
		e.preventDefault();
		gameob.level(1);
	});
	$("#rc").append(start);


	/*
	
	first find angle...
	
	Distance from currentmouse
	-This is a restricted aspect since there is a maximum distance
	
	size
	-this is a restricted aspect since its size can only be within the constaints of the left, right, top and bottom
		-need to find minimum radius possible
	
	
	Resting Time
	
	
	Number on Screen
	
	Target size
	
	= Time it exsists on screen
	
	*/
	

});




</script>