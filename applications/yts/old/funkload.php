<?php

function tempfunk($mang, $google){
$begginer = "http://gdata.youtube.com/feeds/api/users/";
$ender = "&key=AI39si53J06VFj_a6T3bay56rDjFmF-1IlvTS6FqmXgQUbqORQDk0OyUvCV-m7ur9_vAbmhmVGFC_f1TKsyEi1_1deEasGNqSQ&v=2";
if($google == 1){$mang = $begginer.$mang.$ender;}
$handle = fopen($mang, "r");
$manny = stream_get_contents($handle);
fclose($handle);

$manny = str_replace("\r", "", $manny);
$manny = str_replace("\n", "", $manny);
$manny = str_replace("\"", "'", $manny);
$manny = str_replace(":", "", $manny);
$manny = str_replace("&", "&amp;", $manny);

return $manny;
}

?>