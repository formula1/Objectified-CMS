<?php
include 'funkload.php';
$begginer = "http://gdata.youtube.com/feeds/api/users/";
$ender = "&key=AI39si53J06VFj_a6T3bay56rDjFmF-1IlvTS6FqmXgQUbqORQDk0OyUvCV-m7ur9_vAbmhmVGFC_f1TKsyEi1_1deEasGNqSQ&v=2";


function updateOld($artist){

	$tempx = tempfunk($artist."/uploads?max-results=1", 1);
	$found=0;
	$info = new DomDocument;
	$info->preserveWhiteSpace = TRUE;
	$info->load('info.xml');
	$elfeed = $info->getElementsByTagName('feed')->item(0);
	$temper = new DomDocument;
	$temper->preserveWhiteSpace = FALSE;
	$temper->loadXML($tempx);
	$totalresults = $temper->getElementsByTagName('openSearchtotalResults')->item(0)->nodeValue;
	echo "
The total results are".$totalresults;

	foreach ($elfeed->getElementsByTagName('artist') as $tempart) {
		if($tempart->getElementsByTagName('name')->item(0)->nodeValue == $artist){
			$done = $tempart->getElementsByTagName('num')->item(0)->nodeValue;
			$found = 1;
			break;
		}
	}

	if($found == 0){
		$element = $info->createElement('artist');
		$elfeed->appendChild($element);
		$element->appendChild($info->createElement("name", $artist));
		$element->appendChild($info->createElement("num", 0));
		if(!realpath("artists/".$artist)){
			$older = umask(0);
			mkdir("artists/".$artist, 0777);
			umask($older);
		}
		$info->save("info.xml");
		$done = 0;
	}
	echo "
number done: ".$done;

	if($totalresults-$done <= 0){return;}
	if($totalresults-$done < 50){$getsome = $totalresults-$done;}
	else{$getsome = 50;}
	$startentry = $totalresults - ($done + $getsome) + 1;
	$newloc =$artist."/uploads?start-index=".$startentry."&max-results=".$getsome;
	$returned = new DomDocument;
	$returned->preserveWhiteSpace = FALSE;
	$returned->loadXML(tempfunk($newloc, 1));
	$document = floor($done/200);
	if(file_exists($_SERVER{'DOCUMENT_ROOT'}.'artists/'.$artist.'/'.$artist.'-'.$document.'.xml')){
		$trial = new DomDocument;
		$trial->preserveWhiteSpace = FALSE;
		$trial->load('artists/'.$artist.'/'.$artist.'-'.$document.'.xml');
	}else{
		$trial = new DomDocument;
		$trial->preserveWhiteSpace = FALSE;
		$trial->loadXML('<feed> <info> <artist>'.$artist.'</artist> <doc> '.$document.' </doc> </info> </feed>');
		$trial->save('artists/'.$artist.'/'.$artist.'-'.$document.'.xml');
	}
	$meh = $trial->getElementsByTagName('feed')->item(0);


	foreach($returned->getElementsByTagName('entry') as $vid){
		$prep = $vid->getElementsByTagName('mediagroup')->item(0);
		$hope = $trial->createElement('entry');
		$meh->appendChild($hope);
		$hope->appendChild($trial->createElement('id', $prep->getElementsByTagName('ytvideoid')->item(0)->nodeValue));
		$hope->appendChild($trial->createElement('date', $prep->getElementsByTagName('ytuploaded')->item(0)->nodeValue));
		$hope->appendChild($trial->createElement('volume', 0));
		$hope->appendChild($trial->createElement('dury', $prep->getElementsByTagName('ytduration')->item(0)->getAttribute('seconds')));
		$hope->appendChild($trial->createElement('title', $prep->getElementsByTagName('mediatitle')->item(0)->nodeValue));
		$hope->appendChild($trial->createElement('author', $artist));
		$hope->appendChild($trial->createElement('document', floor($done/200)));
		$hope->appendChild($trial->createElement('views', 0));
	}
	$trial->save('artists/'.$artist.'/'.$artist.'-'.$document.'.xml');

	foreach($info->getElementsByTagName('artist') as $tempart){
		if($tempart->getElementsByTagName('name')->item(0)->nodeValue == $artist){
			$element  = $tempart->getElementsByTagName('num')->item(0);
			$newvalue = $tempart->getElementsByTagName('num')->item(0)->nodeValue;
			$tempart->removeChild($element);
			$newelement = $info->createElement('num', $done + $getsome);
			$tempart->appendChild($newelement);
			$info->save("info.xml");
			break;
		}
	}
}

$precursor = tempfunk("TheFightclubvg/subscriptions?max-results=50", 1);
$startup = new DomDocument;
$startup->preserveWhiteSpace = FALSE;
$startup->loadXML($precursor);
$entries = $startup->getElementsByTagName('entry');
$counter = 0;
$temparray = array();
foreach($entries as $thing){
$temparray[$counter] = $thing->getElementsByTagName('ytusername')->item(0)->nodeValue;
echo "

artist name: ".$temparray[$counter];
updateOld($temparray[$counter]);
$counter++;
}

?>
