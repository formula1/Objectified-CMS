<?php

/*
Whats important about this MVC?

Organization

We have an Object
We have how we want the object to be displayed
We have how we want the object to be recieved by javascript aspects

I think before we start creating a restful engine
We should consider making sure our data objects can be created as javascript objects

I suppose right now I'd rather just "preload" anything to any documents that need generic functions....


The important things to me right now are....
-Applications
	-can designate its data objects
		-Each data object has privacy aspects associated to it
		-designate object view
	-designates its initial views
	-designates object list views
-Users
-File Directory View


View
-On login-Refresh view
-On logout-refresh view
-On click a link-shift view

*/


require_once(dirname(__FILE__)."/Mobile_Detect.php");
require_once(__ROOT__."Core/User/user_object.php");


class Request {
    public $url_elements;
    public $verb;
    public $parameters;
	public $device;
	public $mimetype;
	public $user;

 
    public function __construct() {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        $this->url_elements = explode('/', $this->get_path_info());
		$this->parseIncomingParams();
		$this->mimetype = $this->getMimeType();
		if (!class_exists('Mobile_Detect')) { die("no mobile"); }
		$this->device = new Mobile_Detect;
		$this->user = User::getFromSession();
    }
	
	public function getMimeType(){
		if(strrpos(($last = $this->url_elements[count($this->url_elements)-1]), ".") !== false){
			$this->url_elements[count($this->url_elements)-1] = substr($last,0, strrpos($last, "."));
			return substr($last,strrpos($last, ".")+1);
		}else if(isset($this->parameters['format'])) {
			unset($this->parameters['format']);
			return $this->parameters['format'];
		}else{
			return 'html';
		}
	}
 
    public function parseIncomingParams() {
        $parameters = array();
 
        // first of all, pull the GET vars
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $parameters);
        }
 
        // now how about PUT/POST bodies? These override what we got from GET
        $body = file_get_contents("php://input");
        $content_type = false;
        if(isset($_SERVER['CONTENT_TYPE'])) {
            $content_type = $_SERVER['CONTENT_TYPE'];
        }
        switch($content_type) {
            case "application/json":
                $body_params = json_decode($body);
                if($body_params) {
                    foreach($body_params as $param_name => $param_value) {
                        $parameters[$param_name] = $param_value;
                    }
                }
                break;
            case "application/x-www-form-urlencoded":
                parse_str($body, $postvars);
                foreach($postvars as $field => $value) {
                    $parameters[$field] = $value;
 
                }
                break;
            default:
                // we could parse other supported formats here
                break;
        }
        $this->parameters = $parameters;
    }
	
	function get_path_info(){
		if( ! array_key_exists('PATH_INFO', $_SERVER) )
		{
			if($_SERVER['QUERY_STRING'] == "") return $_SERVER['REQUEST_URI'];
			$pos = strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']);
		
			$asd = substr($_SERVER['REQUEST_URI'], 0, $pos - 1);
//			$asd = substr($asd, strlen($_SERVER['SCRIPT_NAME']) + 1);
			
			return $asd;    
		}
		else
		{
			return trim($_SERVER['PATH_INFO'], '/');
		}
	}
	
}	
?>