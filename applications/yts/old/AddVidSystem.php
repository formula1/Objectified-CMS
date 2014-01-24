<?php


function getFileNames($directory){
	if ($handle = opendir($directory)) { 
		$dir_array = array();
		$c = 0;
		while (false !== ($file = readdir($handle))) {
			if($file!="." && $file!=".."){
				$dir_array[$c] = $file;
				echo $dir_array[$c];
				$c += 1;
			}
		}
		closedir($handle);
		return $dir_array;
	}
}


$totaldury = 0;
$bleh = new DomDocument;
$bleh->preserveWhiteSpace = TRUE;
$bleh->load("hpl.xml");

$hours = $bleh->getElementsByTagName('hour');
$time = ceil((time()%86400)/3600);
foreach($hours as $try){
	if($try->getAttribute('e') == $time){$hope = $try;}
}
foreach($hope->getElementsByTagName('entry') as $MayYourSoulBeSaved){
$hope->removeChild($MayYourSoulBeSaved);
}

	$input = new DomDocument;
	$input->preserveWhiteSpace = TRUE;
	$input->load('current.xml');
	$feed = $input->getElementsByTagName("feed")->item(0);

$counteded = 0;
foreach ($feed->getElementsByTagName('entry') as $maybe){
$counteded += $maybe->getElementsByTagname('dury')->item(0)->textContent;
}


$savetime = 5400 - $counteded;

$morecounting = 0;
while($totaldury<$savetime){
	$artistfiles = getFileNames('artists/');
	$new = array_rand($artistfiles);
	$file_array = getFileNames('artists/'.$artistfiles[$new].'/');
	if(count($file_array)>0){
		$rinfo = new DomDocument;
		$rinfo->preserveWhiteSpace = TRUE;
		$rinfo->load( 'artists/' . $artistfiles[$new] . '/' . $file_array[array_rand($file_array)] );
		$tehfile = $rinfo->getElementsByTagName('entry');
		$addup = $tehfile->item(rand(0,$tehfile->length -1));
		$hope->appendChild($bleh->importNode($addup, TRUE));
	$totaldury += $addup->getElementsByTagName('dury')->item(0)->textContent;
		echo '
the total duration added is '.$totaldury;
	$morecounting++;
	}
}
	$bleh->save("hpl.xml");
	$input = new DomDocument;
	$input->preserveWhiteSpace = TRUE;
	$input->load('current.xml');
	$feed = $input->getElementsByTagName("feed")->item(0);
	foreach ($hope->getElementsByTagName('entry') as $try) {
		$feed->appendChild($input->importNode($try, TRUE));
	}
	echo '
the number of videos added is '.$morecounting;
	$input->save('current.xml');
?>