jQuery(function(){
console.log("asdsd");
ap.appendApp("chat", function(url){
	var url = "/applications/chat/";
	var that = this;
	var ssid;
	var comet = new Worker(url+'cometworker.js');
	

	
	this.clearchat = function(){
		jQuery('#chat input[type="text"]').val('');
	};
	
	this.logout = function(user){
		jQuery.ajax(url+"/app.php?action=logout&ssid="+ssid+"&name="+user.nickname).done(function(content){
			var content = jQuery(content);
			ajx.applyChange(content);
			jQuery("#chat .formarea").empty();
			jQuery("#chat .formarea").append(content);
		});
	}
	
	this.close = function(){
		jQuery.ajax(url+"/app.php?action=leave&ssid="+ssid);
		comet.terminate();
		delete comet.worker;
	}
	
	
	this.login = function(){
		jQuery.ajax(url+"/app.php?action=login").done(function(content){
			var content = jQuery(content);
			content = ajx.applyChange(content);
			jQuery("#chat .formarea").empty();
			jQuery("#chat .formarea").append(content);
		});
	}
	
	
	this.cometStart = function(){
		ssid = jQuery("#chat").find("input[name=\"ssid\"]").val();
		var d = new Date();
		comet.postMessage(url+'/public/modifiercheck'+ssid+'.php'+"?time="+d.getTime());
	}

	comet.onmessage = function(event) {
		jQuery("#chat .chatholder").html(event.data);
		that.cometStart();
	}

	
	this.cometStart();
	this.sendMessage = function(){jQuery('#chat message').val("");};
	
});
});