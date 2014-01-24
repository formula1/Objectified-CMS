<?php
$url = getURL(__file__);


?>
<script type="text/javascript" src="<?php echo $url; ?>/ytscontrol.js"></script>
<script type="text/javascript">
// <![CDATA[
ytscontrol = new ytscontrolobject("<?php echo $url; ?>");



jQuery(function(){
	jQuery.getScript("<?php echo $url; ?>/swfobject/swfobject.js", function(){
		var width_of_movie=screen.availWidth/1.33+"px"; 
		var height_of_movie=screen.availHeight/1.79+"px"; 
		var params = { allowScriptAccess: "always", bgcolor: "#cccccc" }; 
		var atts = { id: "ytscontrol" };
		swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&version=3&playerapiid=ytscontrol", "ytapiplayer", width_of_movie, height_of_movie, "8", null, null, params, atts);

	});

});
// ]]>
</script>

<div>
	<div class="info-mang"></div>

	<div id="ytapiplayer">You need Flash player 8+ and JavaScript enabled to view this video.</div>

	<div id="playerstate"></div>
</div>