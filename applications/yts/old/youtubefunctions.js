
/*

if video is on loading
-start timer
-if video is on play
-stop timer
-save as delay


function updateHTML(elmId, value) {         
document.getElementById(elmId).innerHTML = value;       
} 

function setytplayerState(newState) { updateHTML("playerstate", newState); }        

function onYouTubePlayerReady(playerId) { 
ytplayer = document.getElementById("myytplayer");
ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
ytplayer.addEventListener("onError", "onPlayerError");
} 




function onytplayerStateChange(newState) {if(newState==4){getnewvid();}}

function getnewvid() {}


function play() {  if (ytplayer) { ytplayer.playVideo();} }

function pause() {   
ytplayer.loadVideoById(id, parseInt(startSeconds)); 
 if (ytplayer) {  ytplayer.pauseVideo();   
}  } 

function stop() { if (ytplayer) { ytplayer.stopVideo(); } } 
function getPlayerState() { if (ytplayer) { return ytplayer.getPlayerState(); }  } 
function seekTo(seconds) { if (ytplayer) {  ytplayer.seekTo(seconds, true);  }  }  
function getVideoUrl() { alert(ytplayer.getVideoUrl()); }  
function setVolume(newVolume) {  if (ytplayer) { ytplayer.setVolume(newVolume); }  } 
function getVolume() {   if (ytplayer) {  return ytplayer.getVolume(); } } 
function clearVideo() { if (ytplayer) { ytplayer.clearVideo(); } }

function complete_stop() { 
stop();
clearVideo();
seekTo(0);
pause(); 
}

*/

var streamed = 0;
function streamplz(){
streamed = 1-streamed;
	if(streamed == 1){
		function looper(){
			if(ytplayer){return}
			setTimeout('looper', 1);
		}
		looper();

		loadvidmeo();
	}
}


function updateHTML(elmId, value) {
document.getElementById(elmId).innerHTML = value;
}
function setytplayerState(newState) {
updateHTML("playerstate", newState);
}
function onYouTubePlayerReady(playerId) {
ytplayer = document.getElementById("myytplayer");
//setInterval(updateytplayerInfo, 250);
//updateytplayerInfo();
ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
ytplayer.addEventListener("onError", "onPlayerError");
}

var streamerdelay = 0;
var tempTime = 0;
function onytplayerStateChange(newState) {
/*
//if (wait==0){if (ytplayer) {
//if(streamed==1){
//if(ytplayer.getDuration() <= ytplayer.getCurrentTime() + 3){loadvidmeo();wait=1;}
//}
//}}else{if(ytplayer.getPlayerState()==1){wait=0;
//
*/
if(ytplayer){
	if(newState==0&&streamed==1){ loadvidmeo(); }
	if(newState==3){ temptime = new Date(); }
//	if(newState==2){temptime = "wait";}
//	if(newState==1 && (temptime!="wait")){
//		streamerdelay = Math.round((new Date() - temptime)/1000);
//		var newTime = streamerdelay + ytplayer.getDuration();
//		if (newTime > ytplayer.getDuration() + 5) { ytplayer.seekTo(newTime, true); }
//	}
}
}

function onPlayerError(errorCode) {
alert("An error occured: " + errorCode);
}
function updateytplayerInfo() {
updateHTML("bytesloaded", getBytesLoaded());
updateHTML("bytestotal", getBytesTotal());
updateHTML("videoduration", getDuration());
updateHTML("videotime", getCurrentTime());
updateHTML("startbytes", getStartBytes());
updateHTML("volume", getVolume());
}

function streamerNewVideo(id, startSeconds, title, author, stream) {
if (ytplayer) {
ytplayer.loadVideoById(id, parseInt(startSeconds));
ytplayer.pauseVideo();
setTimeout('ytplayer.playVideo();', parseInt(stream)*1000);
tempstr = '<h1>'+title+'</h1><br /><h2>'+author+'</h2>';
document.getElementById("info mang").innerHTML = tempstr;
}
}

function stop() {
if (ytplayer) {
ytplayer.stopVideo();
}
}
function getPlayerState() {
if (ytplayer) {
return ytplayer.getPlayerState();
}
}
function seekTo(seconds) {
if (ytplayer) {
ytplayer.seekTo(seconds, true);
}
}
function seekEnd() {
if (ytplayer) {
var temp = ytplayer.getDuration() - 10;
ytplayer.seekTo(temp, true);
}
}
function getEmbedCode() {
alert(ytplayer.getVideoEmbedCode());
}
function getVideoUrl() {
alert(ytplayer.getVideoUrl());
}
function setVolume(newVolume) {
if (ytplayer) {
ytplayer.setVolume(newVolume);
}
}
function getVolume() {
if (ytplayer) {
return ytplayer.getVolume();
}
}
function clearVideo() {
if (ytplayer) {
ytplayer.clearVideo();
}
} 
function complete_stop() {
stop();clearVideo();seekTo(0);pause();
}
function seekForward(seekTime) {
var newTime = ytplayer.getCurrentTime() + seekTime;
if (newTime < ytplayer.getDuration()) {
ytplayer.seekTo(newTime, true);
}
}
function seekBackward(seekTime) {
var newTime = ytplayer.getCurrentTime() - seekTime;
if (newTime > 0)  {ytplayer.seekTo(newTime, true);} 
}


