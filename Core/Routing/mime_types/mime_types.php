<?php

function get_Mime_Types(){


	function generateUpToDateMimeArray(){
		$s=array();
		$file = file_get_contents("http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types");
		
		foreach(@explode("\n",$file)as $x)
			if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('([^\s]+)',$x,$out)&&($c=count($out[0]))>1){
				for($i=1;$i<$c;$i++)
					$s[$out[0][$i]] = $out[0][0];
			}
			
		return (ksort($s))?$s:false;
	}


	$boo = false;
	if(!file_exists(dirname(__FILE__)."/stored_mime_types.json")) $boo = true;
	else $json = json_decode(file_get_contents(dirname(__FILE__)."/stored_mime_types.json"), true);
	if($boo || $json["time"] < time() -24*60*60){
		$json = array("time"=>time(), "mime"=>generateUpToDateMimeArray());
		file_put_contents(dirname(__FILE__)."/stored_mime_types.json", json_encode($json));	
	}

		return $json["mime"];
}
?>