<?php

if($global_user == null) die();
$data = file_get_contents(dirname(__FILE__)."/chat.txt"); //read the file
$convert = explode("<br/>", $data);
if(count($convert) >9) array_shift($convert);
$convert[count($convert)] = '['.date('H:i').'] '.$global_user->nickname.' : '.htmlentities(rawurldecode($_GET['message']));
$newstring = implode("<br/>",$convert);

$fh = fopen(dirname(__FILE__).'/chat.txt', 'w');
fwrite($fh, $newstring);
fclose($fh);

$fh = fopen(dirname(__FILE__).'/time.txt', 'w');
fwrite($fh, microtime(true)*1000);
fclose($fh);

?>
