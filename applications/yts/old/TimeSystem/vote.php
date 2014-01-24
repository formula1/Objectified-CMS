<?php


function increasevote($updown, $artist){
$info = new DomDocument;
$info->preserveWhiteSpace = TRUE;
$info->load('hpl.xml');

$timered = 1 + floor(time()/(60*60))%24;

foreach($info->getElementsByTagName("entry") as $entry){
	if($entry->getAttribute('hour') == $timered){
		foreach($entry->getElementsByTagName("vote") as $vote){
			if($vote->getAttribute('artist') == $artist){
			$count = $vote->getAttribute('count');
			$vote->setAttribute('count', $updown+$count);
			$found=1;
			break;
			}
		}
		if($found==0){
			$prep = $info->createElement("vote", $artist);
			$prep = $entry->appendChild($prep);
			$prep->setAttribute("artist", $artist);
			$prep->setAttribute("count", $updown);
		}
		$info->save('hpl.xml');
		return;
	}
}

}


?>