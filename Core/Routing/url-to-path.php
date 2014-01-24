<?php

/*
Right now the goal of this is to....
1) Prevent any attempts to access any folders other than Applications and themes

2) allow me to load in important files everytime a site has been called
	-Right now user_object is specifically important
	-later date_object (maybe?) (or at least setting global variables for date...)



*/
require_once dirname(__FILE__)."/route_object.php";
require_once (dirname(__FILE__)."/mime_types/mime_types.php");
require_once __ROOT__."Core/Data/children_detect.php";



 
$mimetypes = get_Mime_Types();
$request = new Request();
$path = $request->url_elements;
array_splice( $path, count($path)-1, 0, "public" );
$pub_prep = implode("/",$path) .".". $request->mimetype;
$filename = substr(__ROOT__,0,strlen(__ROOT__)-1).implode("/",$request->url_elements).".".$request->mimetype;

if( $request->url_elements[1] == "Data" && $request->url_elements[2] != "User"){

	
	$ourclass = $request->url_elements[2];
	
	if(($obs = Extended_Object::find(array("classname" =>$ourclass))) == null) is_404();
	
	include_once($obs->location);
	
	if(is_numeric($request->url_elements[3])){
		//Get the object by ID
		$obs = array(new $ourclass($request->url_elements[3]));
	}else if($request->url_elements[3] == "new"){
	//create new object 
		$obs = array(new $ourclass($request->parameters));
	}else if($request->url_elements[3] == "find"){
	//call find function
		$obs = $ourclass::find($request->parameters);
	}
	
	$finish = "";
	foreach($obs as $ob){
		if($request->mimetype == "json"){
		
		
		}else if($request->mimetype == "html"){
			$finish .= $ob->HTML();		
		}else if($request->mimetype == ""){
		
		}
	}
	
	echo $finish;
	
}else{
/*	require_once(__ROOT__."generic_classes/HTML5/Parser.php");
	
	$basedoc = new DOMDocument();;
	if(file_exists(__ROOT__."theme/html/all.html")){
		$basedoc = $doc = HTML5_Parser::parse(file_get_contents( __ROOT__."theme/html/all.html"));
	}else $basedoc->loadXML("<html><head></head><body></body></html>");
	if($request->device->isMobile() && file_exists(__ROOT__."theme/html/mobile.xsl")){
		$xslt = new XSLTProcessor(); 
		$XSL = new DOMDocument();
		$XSL->load(__ROOT__."theme/html/mobile.xsl");
		$xslt->importStylesheet( $XSL );
		$basedoc = $xslt->transformToXML( $basedoc );
	}else if(file_exists(__ROOT__."theme/html/desktop.xsl")){
		$xslt = new XSLTProcessor(); 
		$XSL = new DOMDocument();
		$XSL->load(__ROOT__."theme/html/desktop.xsl");
		$xslt->importStylesheet( $XSL );
		$basedoc = $xslt->transformToXML( $basedoc );
	}else die("no framework specified");

	die($basedoc->saveXML());
*/
/*
	if($request->user->ID != 1){
		session_start();
		if(isset($_SESSION["allowed_files"]) && in_array($filename,$_SESSION["allowed_files"])){
			$key = array_search ($filename, $_SESSION["allowed_files"]);
			unset($_SESSION["allowed_files"][$key]);
			passThrough($request, $filename);
		}else{
			require_once(__ROOT__."generic_classes/HTML5/Parser.php");

			$_SESSION["allowed_files"] = array();
			ob_start();
			is_default($request);
			
			$string = ob_get_contents();
			ob_end_clean();
			$basedoc = HTML5_Parser::parse($string);
			$xpath = new DOMXpath($basedoc);
			
			$hrefs = array(
					"/",
					"samtobia.com",
					"www.samtobia.com",
					"http://www.samtobia.com",
					"https://www.samtobia.com",
					"http://samtobia.com",
					"https://samtobia.com"
				);

			$check = array(
						"a"=>array("a", "href","/body"),
						"form"=>array("form", "action","/body"),
						"script"=>array("script", "src",""),
						"link"=>array("link", "href","/head"),
						"img"=>array("img", "src","/body"),
						"iframe"=>array("iframe", "src","/body")
					);

			$string;
			$temp = array();
			foreach($check as $k=>$v){
				$string = '/html'.$v[2].'//'.$v[0].'[starts-with(@'.$v[1].', "'
							.implode('")] | /html'.$v[2].'//'.$v[0].'[starts-with(@'.$v[1].', "', $hrefs) 
							. '")]';
				$nodes = $xpath->query($string);
				foreach($nodes as $ele){
					$curtype = $check[$ele->tagName];
					$href = $ele->getAttribute($curtype[1]);
					$p = explode("?", $href);
					$href = $p[0];
					$p = $p[1];
					foreach($hrefs as $poss){
						if(strpos($href, $poss) === 0){
							$href = substr($href,$poss+strlen($poss));
							if(strpos($href, "/") === 0) $href = substr($href,1);
							array_push($temp, __ROOT__.$href);
							$href = "http://www.samtobia.com/".$href;
							if(strlen($p) > 0) $href .= "?".$p;
							$ele->setAttribute($curtype[1], $href);
						}
					}
				}

			}

			$_SESSION["allowed_files"] = $temp;
			echo $basedoc->save();

		}
		//Read the document
		//see what requests another document
		//add that to session as "allowed files"
		
		

	}else */if($request->url_elements[1] == ""){
		//$global_user->toXML();

		include_once dirname(__FILE__)."/../../theme/index.php";
	}else if(
		$request->url_elements[1] == "applications" 
	||	$request->url_elements[1] == "theme"
	||	$request->url_elements[count($request->url_elements)-2] == "public"
	|| file_exists(($filename = dirname(__FILE__)."/../..".$pub_prep))
	){
		passThrough($request,$filename);
	}else if($request->mimetype == "html" && ($classinfo = Extended_Object::find(array("classname"=>$request->url_elements[1]))) != null ){
		include_once($classinfo->location);
	//	include_once(__ROOT__."theme/data.php");	
		
		//If its default, get default
		//If its single, get single information
		//if its a search, display each
		//if its an action, do action
		
		if(!isset($request->url_elements[2])){
			dealWithData($request, $classname::getDefaultData());
		}else if(is_numeric($request->url_elements[2])){
			$classname = $classinfo->classname;
			$ob = new $classname($request->url_elements[2]);
			if(file_exists(__ROOT__."theme/class_html/".$classname.".xsl")){
				$XML = $ob->toXML(); 

				# START XSLT 
				$xslt = new XSLTProcessor(); 
				$XSL = new DOMDocument();
				$XSL->load( __ROOT__."theme/class_html/".$classinfo->classname.".xsl");
				$xslt->importStylesheet( $XSL );
				#PRINT 
				print $xslt->transformToXML( $XML ); 
				die($XML->saveXML());
			}
		
		}else if(in_array($request->url_elements[2],get_class_methods($classname))){
			$prop = new ReflectionProperty($classname, $request->url_elements[2]);
			if($prop->isStatic()){
				$meth = new ReflectionMethod($classname, $request->url_elements[2]);
				die($meth->isStatic());
	//			dealWithData($request, $classinfo["classname"]::
				
			}else is_404();
		}else is_404();

	}else is_404();
}
function call_Theme(){

}

function passThrough($request, $filename){
		global $mimetypes;
		if($request->mimetype == "php"){
			include $filename;
		}else{
			$mimetype = $mimetypes[$request->mimetype];
		
			header('Content-Type: '.$mimetype);
			header("Content-Length: " . filesize($filename));
			$fp = fopen($filename, 'rb');
			fpassthru ( $fp);
		}
}

function is_default($request){
	$filename = __ROOT__."theme/default.php";
	if(file_exists($filename)){	
		include $filename;
	}

}

function is_404(){
	header("HTTP/1.0 404 Not Found");
	print_r($request);
	die("404" . ": Sorry, this page doesn't exsist");
}



?>