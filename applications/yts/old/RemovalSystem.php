<?php

$info = new DomDocument;
$info->preserveWhiteSpace = TRUE;
$info->load('current.xml');
$poop = $info->getElementsByTagName('feed')->item(0);

$rawr = $poop->getElementsByTagName('info')->item(0);
$start = $rawr->getElementsByTagName('start')->item(0)->textContent;
$stream = $rawr->getElementsByTagName('stream')->item(0)->textContent;

$curtime = time();
$lasttime = $rawr->getElementsByTagName('lastupdate')->item(0)->textContent;
$total = $curtime - $lasttime;
echo "time since last removal ".$total."
";

$curvid = $poop->getElementsByTagName('entry')->item(0);
$dury = $curvid->getElementsByTagName('dury')->item(0)->textContent - $start;
$countz = 0;

do{

if($total < $stream){
	$stream = $stream - $total;
	break;
}

	$total = $total - $stream;
	$stream = 0;

if($total < $dury){
	$start = $total+$start;
	break;
}
	$total = $total - $dury;
//delete topnode

	$elfeed = $info->getElementsByTagName('feed')->item(0);
	if($elfeed->getElementsByTagName('entry')->length > 0){
		$elfeed->removeChild($curvid);
		$curvid = $elfeed->getElementsByTagName('entry')->item(0);
		$dury = $curvid->getElementsByTagName('dury')->item(0)->textContent;
	}else{
		include 'SaveVidData.php';
	}

	$start = 0;
	$stream = 10;

	$countz ++;

}while(true);

	$rawr->removeChild($rawr->getElementsByTagName('stream')->item(0));
	$rawr->appendChild($info->createElement('stream', $stream));
	$rawr->removeChild($rawr->getElementsByTagName('start')->item(0));
	$rawr->appendChild($info->createElement('start', $start));
	$rawr->removeChild($rawr->getElementsByTagName('lastupdate')->item(0));
	$rawr->appendChild($info->createElement('lastupdate', $curtime));

$info->save('current.xml');

echo "time till start ".$stream."
time to begin ".$start."
the current time ".$curtime."
the last time ".$lasttime."
".$countz." videos removed";


/*

On click Stream
-Location will be this right here....

2 functions
-remove already seen current.xml
-Set the appropiate time for the stream/start

Get Current Time, Get Last Time

currenttime = time();
lastupdatetime = xml.info.last
totaltimebetween = currenttime - lastupdatetime;
currentduration = topnode's duration - current duration;
currentstream = current stream;

do{
if(totaltimebetween < currentstream){
currentstream = currentstream - totaltimebetween;
currentduration = 0;
break;
}

totaltimebetween = totaltimebetween - currentstream;
workit = "vid";

if(totaltimebetween < currentvidtimeleft){
currentstream = 0;
currentduration = totaltimebetween;
break;
}

totaltimebetween = totaltimebetween - currentvidtimeleft;
catch(delete that node)
currentduration = topnode's duration
currentstream = 0;
workit = "stream";

}while(true);

xml.info.stream = currentstream;
xml.info.duration = currentduration;
xml.info.lastload = currenttime;


-Loop until No more videos





*/


?>