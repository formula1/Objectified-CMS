var xmlhttp=new XMLHttpRequest();
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
	self.postMessage(xmlhttp.responseText);
    }
  }

self.onmessage = function(event) {
	xmlhttp.open("GET",event.data,true);
	xmlhttp.send();
};
