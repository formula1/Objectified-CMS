var xmlhttp;

function wait(delay){
var startTime = new Date();
var endTime = null;
do {endTime = new Date();} 
while ((endTime - startTime) < delay);
}



function verify() 
{ 
 // 0 Object is not initialized 
 // 1 Loading object is loading data 
 // 2 Loaded object has loaded data 
 // 3 Data from object can be worked with 
 // 4 Object completely initialized 
 if (xmlDoc.readyState != 4) 
 { 
   return false; 
 } 
}

function JStoPHP(arrayz) {

var countinging = 0;
while(document.getElementById("frameid"+countinging)){countinging++;}

var newdiv = document.createElement('iframe');
newdiv.setAttribute('id', 'frameid'+countinging);
counter = 0;
var tempstring = "/yts/PHPtoJS.php" + "?framename=frameid" + countinging;
while(arrayz[counter]){
tempstring +=  "&loc" + (counter+1) + "="+arrayz[counter][0];
if(arrayz[counter][3] != ""){ tempstring += "&loc" + (counter+1) + "return=" + arrayz[counter][3]; }
tempstring += "&loc"+ (counter+1) + "var2=" + arrayz[counter][2] + "&loc" + (counter+1) + "var1=" + arrayz[counter][1];
counter++;
}

newdiv.setAttribute('src', tempstring);
newdiv.style.display = 'none';
document.body.appendChild(newdiv);
}


function xmlstuff(bleh){
if (window.DOMParser) {
 var parser=new DOMParser();
 var xmlDoc=parser.parseFromString(bleh,"text/xml");
} else { // Internet Explorer 
 var xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
 xmlDoc.async="false";
 xmlDoc.loadXML(bleh);
}
return xmlDoc;
}




function XMLPrep(phpfunk){
	if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }
	else{ xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){ 
		return xmlhttp.responseXML;
		}
	}
	xmlhttp.open("GET",phpfunk, false);
	xmlhttp.send();
	
}




/*
function poopoomcshoot(){
var xmlDoc = requesting("/yts/current.xml", 0);
var elvid=xmlDoc.getElementsByTagName("id")[0].childNodes[0].nodeValue;
loadNewVideo(elvid, 20);
}

window.onload=setTimeout('poopoomcshoot()',2000);


http://gdata.youtube.com/feeds/api/users/FightClubVG/subscriptions


*/