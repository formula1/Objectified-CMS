<?php

$ct = floor($_GET['time']);
$m = floor(microtime(true)*1000);
$meh = floor(file_get_contents(dirname(__FILE__).'/../time.txt')); //read the file

while(($meh < $m) && (floor(microtime(true)*1000) < $m+2000)){
	$meh = floor(file_get_contents(dirname(__FILE__).'/../time.txt'));
	usleep(1000);
}

$chat = file_get_contents(dirname(__FILE__).'/../chat.txt');
/*$chat .= 'Client to server_____= '.$ct."<br/>";
$chat .= 'server start_________= '.$m."<br />";
$chat .= 'other doc update_____= '.$meh."<br/>";
$chat .= 'Server to Cient______= '.floor(microtime(true)*1000) ."<br />";*/
echo $chat;
?>
