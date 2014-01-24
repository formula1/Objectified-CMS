<?php
include "../../genericfunk.php";
$url = getURL(__file__);
?>

<script type="text/javascript" >
var chatobject = function(){
	var that = this;
	var url ="<?php echo $url;?>";
	var comet = new Worker(url+'/cometworker.js');
	this.cometStart = function(){
		var d = new Date();
		comet.postMessage(url+'/public/modifiercheck'+jQuery("#chat input[name=\"ip\"]").val()+'.php'+"?time="+d.getTime());
	}
	
	comet.onmessage = function(event) {
		jQuery("#chat .chatholder").html(event.data);
		that.cometStart();
	}
	
	this.join = function(){
		jQuery.post(url+"/app.php?action=enter", $( "#chat form" ).serialize()).done(function(content){
			console.log(content);
			jQuery("#chat .nick").remove();

			if(content.indexOf("ip:") != -1){
				jQuery.ajax(url+"/app.php?action=message").done(function(content){
					jQuery("#chat").append(content);
				});
			}else{
				var co = content;
				jQuery.ajax(url+"/app.php?action=capta").done(function(content){
					jQuery("#chat").append(content);
					jQuery("#chat form").prepend("<h3 style=\"color:red\">"+co+"</h3>");
				});
			}
		});
	}
	
	this.sendMessage = function(){
		jQuery.ajax(url+
			"/newmessage.php?name="
			+encodeURIComponent(jQuery('#chat input[name="nick"]').val())
			+"&message="+encodeURIComponent(jQuery('#chat textarea').val())
		);
		jQuery('#chat textarea').val("");
	};
	
	this.leave = function(){
		jQuery.ajax(url+"/app.php?action=leave");
	}
};

var chatob = new chatobject();

jQuery(window).unload(function(){
	chatob.leave();
});

</script>

