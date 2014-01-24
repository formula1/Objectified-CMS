<?php
	header('Content-Type: application/javascript');
	include dirname(__FILE__)."/../../../genericfunk.php";
?>


function app_manager(){
	that = this;
	this.apps = {};

	this.appendApp = function(appname, appFunk){
		console.log("appending");
		that.apps[appname] = new appFunk();
	};
/*
	this.openApp = function(name){
		if(typeof apps[name] !== 'undefined') return;
		
		apps[name] = {url:mb, login:null,logout:null,open:function(){},close:function(){}};
		apps[name].open();
	};

	this.closeApp = function(name){
		console.log("deleting"+name);
		apps[name].close();
		delete apps[name];
	};
	*/
	this.login = function(user){
		for(app in that.apps){
			if(that.apps[app].login != null)
				that.apps[app].login(user);
			else{
				var frame = $(".psuedoframe[data-framename="+app+"]");
				shiftScene(frame,{url:frame.attr("data-src"), method:"GET"});
			}
			
		}
	};
	
	this.logout = function(user){

		for(app in that.apps){
			if(that.apps[app].logout != null)
				that.apps[app].logout(user);
			else{
				var frame = $(".psuedoframe[data-framename="+app+"]");
				shiftScene(frame,{url:frame.attr("data-src"), method:"GET"});
			}
		}
	};
}

var ap;
jQuery(function(){
	ap = new app_manager();
});
