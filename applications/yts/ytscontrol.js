var ytscontrolobject = function (url) {
	console.log("prepare");
	var url = url;
	this.ytplayer;
	this.streamed = 0;
	this.loadvideo = function (){
	
		var yt = this.ytplayer;
	
		console.log("loading...");
		jQuery.ajax(url+"/app.php?action=current").done(function(content){
			console.log(content);
			var info = JSON.parse(content);
			jQuery(".info-mang").html('<h1>'+info.title+'</h1><br /><h2>'+info.author+'</h2>');
			if(yt){
				yt.setVolume(25);
				yt.loadVideoById(info.id, parseInt(info.time));
				yt.playVideo();
	//			ytplayer.pauseVideo();
	//			setTimeout('ytplayer.playVideo();', parseInt(info.streamtime)*1000);
			}
		});
	};

};

	
	
ytscontrolobject.prototype.onYouTubePlayerReady = function(playerid){
	this.ytplayer = document.getElementById(playerid);
	this.ytplayer.addEventListener("onStateChange", "ytscontrol.statechange");
	this.streamed = 1;
	this.loadvideo();
};

ytscontrolobject.prototype.statechange = function(newState){
		console.log(newState);
		if(this.ytplayer){
			if(newState===0&&this.streamed===1){ this.ytplayer.clearVideo(); this.loadvideo(); }
		//	if(newState==3){ temptime = new Date(); }
		//	if(newState==2){temptime = "wait";}
		//	if(newState==1 && (temptime!="wait")){
		//		streamerdelay = Math.round((new Date() - temptime)/1000);
		//		var newTime = streamerdelay + ytplayer.getDuration();
		//		if (newTime > ytplayer.getDuration() + 5) { ytplayer.seekTo(newTime, true); }
		//	}
		}
};
