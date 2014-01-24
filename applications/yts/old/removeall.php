<?php

$info = new DomDocument;
$info->preserveWhiteSpace = TRUE;
$info->load('info.xml');
$elfeed = $info->getElementsByTagName('feed')->item(0);
foreach ($elfeed->getElementsByTagName("artist") as $tempart){
	$elfeed->removeChild($tempart);
}
$info->save('info.xml');


$info = new DomDocument;
$info->preserveWhiteSpace = TRUE;
$info->load('current.xml');
$elfeed = $info->getElementsByTagName('feed')->item(0);
foreach ($elfeed->getElementsByTagName("entry") as $tempart){
	$elfeed->removeChild($tempart);
}
$info->save('current.xml');



?>