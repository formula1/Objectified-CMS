<?php 

define('__ROOT__', __DIR__ .'/');
error_reporting(E_ALL ^ E_NOTICE);

function getURL($file){

	$dirname = realpath(dirname(__FILE__));
	$removalpath =  substr(realpath(dirname($file)), strlen($dirname));	
	$http = (isset($_SERVER["HTTPS"]))?"https://":"http://";
	return $http.$_SERVER["HTTP_HOST"].$removalpath."/";
}



function userLoggedIn(){
	return null;
}

function findFileIn($searched, $directory=__DIR__){
	$rec = function($main, $searched, $rec){
		$main .= "/";
		$dirHandle = opendir($main);
		while($file = readdir($dirHandle)){
			if($file == '.' || $file == '..') continue;
			if($file == $searched) return $main.$file;
			if(is_dir($main.$file))
				if(($hoped = $rec($main.$file, $searched, $rec)) !== false){
				return $hoped;
				}
		}
		return false;
	};
	return $rec($directory, $searched, $rec);
}


function cleanPath($path){
	while($bi = strpos($path, "/..")){
		$first = substr($path,0, $bi);
		$first = substr($first, 0, strrpos($first, "/"));
		
		$second = substr($path, $bi+3);
		$path = $first.$second;
	}

	return $path;
}


function grabHTML($path){
	if(!file_exists($path)) throw new Exception("Non exsistant");
	if("html" != pathinfo($path, PATHINFO_EXTENSION)) throw new Exception("currently not running php files");
	$out = file_get_contents($path);
	$out = preg_replace("/\t+|\r+|\n+/", "", $out);
	return $out;
}

function dialogHTML(){ 
ob_start();
?>
<div class="dialog">
	<header>
		<h3></h3>
		<a class="close" href="#">Close</a>
	</header>
	<section class="content"></section>
</div>
<?php
$out = ob_get_contents();
ob_end_clean();

$out = preg_replace("/\t+|\r+|\n+/", "", $out);
return $out;
}

function coreDB(){
    var_dump(debug_backtrace());
	$json = json_decode(file_get_contents(dirname(__FILE__)."/db.json"));
	$con=mysqli_connect($json->host,$json->user, $json->password,$json->db);
	if (mysqli_connect_errno())
	{
		return null;
	}else{
		return $con;
	}
	
}

function postTo($url, $params, $port = 80){
	foreach ( $params as $key => $value) {
		$params[] = $key . '=' . $value;
	}
	$post_string = implode ('&', $params);
	$post_string =  $post_string;
	
	$ch = curl_init();

	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	curl_setopt($ch,CURLOPT_POST, count($params));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $post_string);

	$result = curl_exec($ch);

	curl_close($ch);
	return $result;

}


?>