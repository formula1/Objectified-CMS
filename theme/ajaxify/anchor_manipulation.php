<?php


function anchor_manip($content){

require_once(__ROOT__."generic_classes/HTML5/Parser.php");

$doc = HTML5_Parser::parse($content);
$xpath = new DOMXpath($doc);


$hrefs = array(
		"/",
		$_SERVER["SERVER_NAME"],
		"www.".$_SERVER["SERVER_NAME"],
		"http://www.".$_SERVER["SERVER_NAME"],
		"https://www.".$_SERVER["SERVER_NAME"],
		"http://".$_SERVER["SERVER_NAME"],
		"https://".$_SERVER["SERVER_NAME"]
	);

	
$anchor_selector = '/html/body//a[starts-with(@href, "'.implode('")] | /html/body//a[starts-with(href, "', $hrefs) . '")]';
$form_selector = '/html/body//form[starts-with(@action, "'.implode('")] | /html/body//form[starts-with(@action, "', $hrefs) . '")]';

$fs_and_as = $xpath->query($anchor_selector." | ".$form_selector);


foreach($fs_and_as as $ele){
	if($ele->tagName == "a") $href = $ele->getAttribute("href");
	else $href = $ele->getAttribute("action");
	$ohref = $href;
	$p = explode("?", $href);
	$href = $p[0];

	$p = $p[1];
	foreach($hrefs as $poss){
		if(strpos($href, $poss) !== false){
			if($href == $poss){ $ele->setAttribute("/"); continue 2;}
			$href = substr($href,$poss+strlen($poss));
			if(strpos($href, "/") === 0) $href = substr($href,1);
		}
	}

	if( url_2_App($href)){
		$end = strrpos($href,"/app.php");
		$path = substr($href,0, $end);
		$app = substr($path, strripos($path,"/")+1);
		$ele->setAttribute("target", $app);
	}else if(url_2_Data($href)){
		$temp = substr($href, stripos($href, "data/")+5);
		$temp = substr($temp, 0,stripos($temp, "/"));
		
		$obs = Extended_Object::find(array("classname"=>$temp));
		if(count($obs) == 0) throw new Exception("nonexsistant Object");
		$ob = $obs[0];
		if($ob->default_app == "none")	$ele->setAttribute("target", "_blank");
		else $ele->setAttribute("target", $ob->default_app);
	}else if($ele->getAttribute("target") == "none" || $ele->getAttribute("target") == "_none" || $ele->getAttribute("target") == "function"){
		//$ele->setAttribute("target", "_none");
	}else{
		$ele->setAttribute("target", "_self");
	}

	$href = "/".$href;
	$href .= ($p == "")?"":"?".$p;
	if($ele->tagName == "a") $ele->setAttribute("href", $href);
	else $ele->setAttribute("action", $href);
}

return $doc->saveHTML();

}

function url_2_App($href){
return (strripos($href,"/app.php") !== false);
}

function url_2_Data($href){
return (stripos($href,"Data/") === 0);
}


?>