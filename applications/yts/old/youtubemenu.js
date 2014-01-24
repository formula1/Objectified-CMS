/*Just for loading in users from youtube. and defining function for loading the video data of each user. Include li tags on each and onclick event with the name*/


var ender = "?max-results=50";
var typerz;
var xmlz = new Array();
var numz = new Array();


function Tempuser(texting){
var equiv = xmlstuff(texting);
var prep1, prep2;
prep1 = equiv.getElementsByTagName("feed");
prep2 = prep1[0].getElementsByTagName("entry");
xmlz[1] = prep2;
numz[1] = 0;
nextTen(1, "vigeos", 1);
}

function rundemnewbs(texting){
var prep1, prep2;
var equiv = xmlstuff(texting);
prep1 = equiv.getElementsByTagName("feed");
prep2 = prep1[0].getElementsByTagName("entry");
xmlz[0] =prep2;
numz[0] = 0;
nextTen(1, "newbies", 0);
}

function loaduzerz(){
var arrerz = [["funkload.php", "FightClubVG/subscriptions"+ender, 1, 'subsdos']];
JStoPHP(arrerz);
typerz = 'subs';
}

function subsdos(texting){
var equiv = xmlstuff(texting);
prep1 = equiv.getElementsByTagName("feed");
equiv = prep1[0].getElementsByTagName("entry");

var tooser;
for(x in equiv){
try
	{
tooser = equiv[x].getElementsByTagName("ytusername")[0].childNodes[0].nodeValue;
document.getElementById("uzerz").innerHTML += '<li><a onclick=\"loaduzvidz(\'' + tooser +  '\', 1);\">' + tooser + "</a></li>";  
	}
catch(err)
  {
  }
}
}

function loadnewbies(){
typerz = 'new';
var arrerz = [["funkload.php", "FightClubVG/newsubscriptionvideos"+ender, 1, 'rundemnewbs']];
JStoPHP(arrerz);
}

function loaduzvidz(uzer){
var arrerz = [["funkload.php", uzer+"/uploads"+ender, 1, 'Tempuser']];
JStoPHP(arrerz);
typerz = 'user';
}


function nextTen(posneg, location, refloc){

var tempstr = "";
var prep; 
var countref;
var tempnum;
var tempxml;
countref = xmlz[refloc].length - 1;
if (countref>10) {
if (posneg == 1){
if(numz[refloc] > countref){numz[refloc] = 0;}
numz[refloc] += 10;
}else{
numz[refloc] += -10;
if(numz[refloc]<=0){numz[refloc] = countref+10-countref%10;} 
}
}else{numz[refloc] = 10;}

tempxml = xmlz[refloc];
tempnum = numz[refloc];

for (var x=tempnum-10;x<tempnum;x++) {
try{
if(x<=countref){

	prep = tempxml[x].getElementsByTagName("mediagroup")[0];
var titled = (prep.getElementsByTagName("mediatitle")[0].childNodes[0].nodeValue).replace(/\'/g, "");
var name = tempxml[x].getElementsByTagName("author")[0].getElementsByTagName("name")[0].childNodes[0].nodeValue;
var tempthumb = "http://"+prep.getElementsByTagName("mediathumbnail")[0].getAttribute("url").substring(6);
	tempstr += "<li class=\"vidmang\" >"
	tempstr += "<a onclick=\"loadNewVideo(\'" + prep.getElementsByTagName("ytvideoid")[0].childNodes[0].nodeValue + "\', 0, \'"+titled+"\', \'"+name+"\')\" >";

	tempstr += prep.getElementsByTagName("mediatitle")[0].childNodes[0].nodeValue;
	tempstr += "<br />Author: "+name+"<br />";

	tempstr += "<br /><img src=\""+tempthumb+ "\" style=\"height:97;width:130\" /><br />";
	tempstr += "Length: "+Math.floor(prep.getElementsByTagName("ytduration")[0].getAttribute("seconds")/60)+" minutes and "+prep.getElementsByTagName("ytduration")[0].getAttribute("seconds")%60+" seconds";
	tempstr += "</a></li>";
}
}catch(err){}
}
document.getElementById(location).innerHTML=tempstr;
}


function tempdos(texting){
var equiv = xmlstuff(texting);
var feed = equiv.getElementsByTagName("feed")[0];

var info = feed.getElementsByTagName("info")[0];
var streamtime = info.getElementsByTagName("stream")[0].childNodes[0].nodeValue + streamerdelay;
var starttime = info.getElementsByTagName("start")[0].childNodes[0].nodeValue;
var watchers = info.getElementsByTagName("watcher")[0].childNodes[0].nodeValue;


var video = feed.getElementsByTagName("entry")[0];

var elvid = video.getElementsByTagName("id")[0].childNodes[0].nodeValue;
var dury = video.getElementsByTagName("dury")[0].childNodes[0].nodeValue;
var titlio = video.getElementsByTagName("title")[0].childNodes[0].nodeValue;
var volu = 25;
//video.getElementsByTagName("volume")[0].childNodes[0].nodeValue
var author = video.getElementsByTagName("author")[0].childNodes[0].nodeValue;
setVolume(volu); 
streamerNewVideo(elvid, starttime, titlio, author, streamtime);
}



function loadvidmeo(){
function looper(){
	if(ytplayer){return}
	setTimeout('looper', 1);
}
looper();
JStoPHP([["RemovalSystem.php", "", "", ""], ["funkload.php", "current.xml", "", "tempdos"]]);

}



function loademup(){
streamplz();
loaduzerz();
loadnewbies();
}



window.onload=loademup;
