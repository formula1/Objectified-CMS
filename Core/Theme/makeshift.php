<?php

/*

whenever a data call is made to display
change the header




*/


function doHTML($maindata){
	include dirname(__FILE__)."/phpQuery-onefile.php"
	include __ROOT__."/theme/directions.php";
	$base = file_get_contents(dirname(__FILE__)."/all.html");
	if($maindata == null){
		if(file_exists(__ROOT__."/theme/index.html")){
		
		
		}else if(file_exists(__ROOT__."/theme/index.php")){
		
		}else{
		
		/*
			foreach data type accessible by current user
				-Display a link to the archive
		*/
		
		$all = phpQuery::newDocumentFileXHTML(dirname(__FILE__)."/all.html");
		
		if ($handle = opendir(__ROOT__."/theme/")) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
				
				}
			}
			closedir($handle);
		}
	}
}
?>