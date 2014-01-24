var url;
var start;
var stream=5;
var end;
var on=1;


//One issue is that I need to be able to get a div

function getinfo(){
	var error = 1;
document.getElementById("holder").innerHTML = '<img src=\"plzwait.png\" />';
	while(error==1){
		try{
			if (window.XMLHttpRequest){xhttp=new XMLHttpRequest();
}			
else
{
xhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
			xhttp.open("GET","current.xml",false);
			xhttp.send("");
			xmlDoc=xhttp.responseXML; 
			url=xmlDoc.getElementsByTagName("url")[0].nodeValue;
			start=xmlDoc.getElementsByTagName("start")[0].nodeValue;
			stream=xmlDoc.getElementsByTagName("stream")[0].nodeValue;
			end=xmlDoc.getElementsByTagName("endtime")[0].nodeValue;
			error=0;
			xmlDoc=null;
		}catch(Exception){
document.getElementById("holder").innerHTML = '<img src=\"plzwait.png\" />';
			setTimeout(getinfo, 1000);
		}
	}
	while(stream>0){
setTimeout(getinfo, 1000);
		stream+=-1;
	}
}


function startered(){
		getinfo();
		while(start<end){
			setTimeout(startered, 1000);
			start+=1;
		}
}

var params = { allowScriptAccess: "always" };
var atts = { id: "myytplayer" };
swfobject.embedSWF("http://www.youtube.com/v/"+url+"?enablejsapi=1&playerapiid=ytplayer", "ytapiplayer", "320", "240", "8", null, null, params, atts);


function onYouTubePlayerReady(playerID) {
	ytplayer = document.getElementById("myytplayer");
	ytplayer.setSize(320,240);
	ytplayer.addEventListener("onStateChange", "onytplayerStateChange");
	ytplayer.playVideo();
}

function onytplayerStateChange(newState){
	if(newState===0){
		start=end;
		startered();
	}
}