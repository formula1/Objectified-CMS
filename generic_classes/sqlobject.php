<?php
require_once (dirname(dirname(__FILE__))."/genericfunk.php");
include_once ( __ROOT__."Core/Data/children_detect.php");


abstract class SqlObject{
	
	protected static $indexes = array("ID"=>array("searchable"=>true,"type"=>"integer","default"=>"++","primary"=>true));
	
	public static function reFormat(){
		$cn = get_called_class();
		$con = coreDB();
		$result = mysqli_query($con, "SELECT COUNT(*) FROM ".$cn."s");
		if(mysqli_errno($con)) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "\n" . $message);
		$data=mysqli_fetch_assoc($result);
		mysqli_close($con);
		while($data["total"] > 0){
			$items = $cn::find(array(),100);
			foreach($items as $i){
				foreach(static::$indexes as $key=>$value)
					$i->set($key,$i->$key);
			}
			$data["total"] -= 100;
		}
	}
		
	public function getURL(){
		return "/Data/".get_called_class()."/".$this->ID;
	}
	
	public function toXML($recursive = true){
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML('<?xml version="1.0" encoding="ISO-8859-1"?><'.get_called_class().'></'.get_called_class().'>');
		$root = $xmlDoc->documentElement;
		foreach(static::$indexes as $key=>$i){
			$root->appendChild(($temp = $xmlDoc->createElement($key)));
			if(strpos($i["type"], "array") !== false){
				foreach($this->$key as $ak=>$av){
					$ak = (is_numeric($ak))?"item":$ak;
					$temp->appendChild($xmlDoc->createElement($ak, $av));					
				}
			}else if($i["type"] == "string:html"){
//				$doc->createTextNode('bar & baz')
			}else if(strpos($i["type"], "integer:") === 0 && $recursive){
				$clasname = explode(":",$i["type"]);
				$obj = Extended_Object::find(array("classname"=>$clasname[1]));
				include_once($obj->location);
				$classname = $obj->classname;
				$nextOb = new $classname($this->$key);
				$node = $xmlDoc->importNode($nextOb->toXML(false)->documentElement, true);
				$temp->appendChild($node);
			}else{
				$temp->appendChild($xmlDoc->createTextNode($this->$key));
			}
		}
		return $xmlDoc;
	}
	
	public function HTML(){
		if(($file = findFileIn(get_called_class().".html", __ROOT__."/theme/class_html"))){
			$string = grabHTML($file);
			$inputs = explode("<?", $string);
			while(count($inputs) > 1){
				$end = strpos($inputs[1], "?>");
				$wanted = substr($inputs[1], 0, $end);
				$inputs[1] = $this->$wanted.substr($inputs[1], $end+2);
				$inputs[0] .= $inputs[1];
				array_splice($inputs, 1, 1);
			}
			return $inputs[0];
		}else if(($file = findFileIn(get_called_class().".php", __ROOT__."/theme/class_html"))){
			include $file;
			
			return $tempFunk($this);
		}else if(($string = $this->getHTML()) !== false){
			return $string;
		}else{
			print_r($this);
		}
	}
	
	
	protected function getHTML(){
		return false;
	}
	
	public static function init(){
	
		if(get_called_class() != "Extended_Object" 
		 && count(Extended_Object::find(array("classname"=>get_called_class()))) == 0){
			$reflector = new ReflectionClass(get_called_class());
			$fn = $reflector->getFileName();
			
			$message = "INSERT INTO Extended_Objects (classname, location, default_app) VALUES ('".get_called_class()."', '".$fn."', '".static::getDefaultApp()."')";
			$con = coreDB();
			mysqli_query($con, $message);
			if(mysqli_errno($con)) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "\n" . $message);

			mysqli_close($con);
		}
		
		$postpend = "";
		$message = "CREATE TABLE IF NOT EXISTS ".get_called_class()."s
		(";
		$cvs = get_class_vars(__CLASS__);
		
		$indexi = array_merge(static::$indexes, self::$indexes);
		
		foreach($indexi as $key => $v){
			if($v["primary"] && $key != "ID") throw new Exception("Primary Key can only be Id");
		
		
			$message .= $key." ";
			$types = explode(":", $v["type"]);
			if($types[0] == "integer") $message .= " INT ";
			else if($types[0] == "boolean") $message .= "BOOLEAN ";
			else if($types[0] == "string" && count($types) > 1 && is_numeric($types[1])) $message .= "VARCHAR(".$types[1].") ";
			else if($types[0] == "string" && count($types) > 1 && $types[1] != "enum") $message .= "VARCHAR(128) ";
			else if($types[0] == "string" && count($types) == 1) $message .= "MEDIUMTEXT ";
			else if($types[0] == "string" && count($types) > 1 && $types[1] == "enum"){
				$message .= "ENUM ";
				$enums = explode("|", $types[2]);
				$enums = implode("', '", $enums);
				$message .= "('".$enums."') ";
			}else if($types[0] == "string" && count($types) > 1 && $types[1] == "rand") $message .= "VARCHAR(128) ";
			else if($types[0] == "aarray") $message .= "MEDIUMTEXT ";
			else if($types[0] == "array") $message .= "MEDIUMTEXT ";
			
			if($v["not null"] || $v["primary"]) $message .= "NOT NULL ";
			if($v["primary"]) $message .= "PRIMARY KEY ";
			if(isset($v["default"]) && $types[0] != "array" &&($types[0] != "string" || count($types) > 1)){
				if($v["default"] == "++") $message .= "AUTO_INCREMENT ";
				else $message .= "DEFAULT ".self::formatForSQL($key,$v["default"]);
			}
			$message .= ", ";
			if($types[0] == "integer" && count($types) > 1){
				$postpend .= "FOREIGN KEY (".$key.") REFERENCES ".$types[1]."s(ID), ";
			}
			if($v["unique"]){
				$postpend .= "UNIQUE (".$key."), ";
			}
			
		}
		
		$message .= $postpend;
		$message = substr($message, 0, -2);
		$message .= " )";
		
		$con = coreDB();
		mysqli_query($con, $message);
		if(mysqli_errno($con)) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "\n" . $message);


		mysqli_close($con);
	}
	
	abstract protected function pre_construct(&$args);
	abstract protected function post_construct(&$args);
	abstract protected function post_get();
	
	public function __construct() {
	
		if("Extended_Object" == get_called_class() || count(Extended_Object::find(array("classname"=>get_called_class()))) == 0){
			$curclass = get_called_class();
			$curclass::init();
		}
		
		
		$args = func_get_args();
		if(count($args) == 0) throw new Exception("Cannot Create Empty Object of Type:".get_class());
		if(count($args) > 1) throw new Exception("need either an array or id of this class:".get_called_class());
		$args = $args[0];
		if(is_numeric($args)) $args = intval($args);

		if(gettype($args) == "integer") $this->__construct_integer($args);
		else{
			$this->pre_construct($args);
			foreach(static::$indexes as $key => $value){
				if($value["not null"] && !array_key_exists($key, $args)) throw new Exception("Your missing key:".$key." to construct an instance of Class:".get_called_class());
				if(isset($value["default"]) && $value["types"] == "array" && !isset($args[$key])) $args[$key] = $value["default"];
				else if(!isset($args[$key])) continue;
				
				self::errorChecking($key, $temp = $args[$key], $args);
				
			}
			
			$message = "INSERT INTO ".get_called_class()."s";
			$kmes = "";
			$vmes = "";
			foreach($args as $key => $value){
				$kmes .= $key.", ";
				$vmes .= self::formatForSQL($key, $value).", ";
			}
			$kmes = substr($kmes, 0, -2);
			$vmes = substr($vmes, 0, -2);
			
			$con = coreDB();
			mysqli_query($con, $message ." ( " . $kmes . " ) VALUES ( " . $vmes . " ) ");
			if(mysqli_errno($con)) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "\n" . $message ." ( " . $kmes . " ) VALUES ( " . $vmes . " ) ");
			$id = intval(mysqli_insert_id($con));

			mysqli_close($con);
			
			$this->__construct_integer($id);
			
			$this->post_construct($args);
		}
		$this->post_get();
	}
	
	protected static function sqlSelection(){ return "*"; }
	
	protected function __construct_integer($id){
		$con = coreDB();
		$result = mysqli_query($con, "SELECT ".static::sqlSelection()." FROM ".get_called_class()."s WHERE ID=".$id);
		if(!$result) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "<br />" ."SELECT * FROM ".get_called_class()."s WHERE ID=".$id);
		if($cl_data = mysqli_fetch_array($result, MYSQL_ASSOC)){
			foreach($cl_data as $key => $value){
				if(strpos(static::$indexes[$key]["type"], "array") === 0){
					if(strpos($value, "|") !== false)				$value = explode("|", $value);
					else($value = json_decode($value));
				}else if(strpos(static::$indexes[$key]["type"], "aarray") === 0){
					$temp = explode("|", $value);
					$value = array();
					foreach($temp as $t){
						$t = explode(":", $t);
						$value[$t[0]] = $t[1];
					}
				}else{
					if(strpos(static::$indexes[$key][$type], "integer") === 0) $value = intval($value);
				}
			
				$this->$key = $value;
			}
			mysqli_close($con);
		}else{
			mysqli_close($con);
			throw new Exception("Is not a valid ".get_called_class()." ID: ".$clockin_id);
		}
	}
		
	public function getPropArray(){
		return get_object_vars($this);
	}
	
	private static function errorChecking($key, &$value, &$args){
		if(!array_key_exists($key, static::$indexes)) throw new Exception("this key:".$key." does not exsist in Class:".get_called_class());
		if(!isset(static::$indexes[$key]["not null"]) && $value == null) return;
		$temp = static::$indexes[$key];
		$types = explode(":", $temp["type"]);

		if(gettype($value) == "object"){
			if($types[0] == "integer" && count($types) >1 && get_class($value) == $types[1]){
				$args[$key]= intval($value->ID);
				$value  = intval($value->ID);
			}else throw new Exception("the value of key:".$key." is of object type:".get_class($value)." needs to be:".$types[1]);
		}
		
		if(is_numeric($value)){
			$args[$key]= intval($value);
			$value  = intval($value);
		}
		
		if(gettype($value) != $types[0]) throw new Exception("the value of key:".$key.":".$value." is not of type:".$types[0]."it is a :".gettype($value));

		if(count($types) >1){
			if($types[0] == "string"){
				if($types[1] == "enum" && false===strpos($types[2],$value)) throw new Exception("the value:".$value." of key:".$key." is not of a valid value:".$types[2]);
				if($types[1] == "email" && !filter_var($value, FILTER_VALIDATE_EMAIL)) throw new Exception("the value of key:".$key." is not a valid email");
			}
			if($types[0] == "integer"){
				$find = new $types[1]($value);
				if(get_class($find) != $types[1]) throw new Exception("the value of key:".$key."|".get_class($find)." is not of Class:".$types[1]);
			}
		}

	}
	
	public function set(){
		$args = func_get_args(); 
		if(count($args) == 2){
			$args = array($args[0]=>$args[1]);
		}else if(count($args) == 1){
				$args = $args[0];
		}else throw new Exception("incorrect amount of arguments");

		foreach($args as $key => $value){
			self::errorChecking($key, $value, $args);
		}
		
		$message = "UPDATE ".get_called_class()."s SET ";
		$kmes = "";
		foreach($args as $key => $value){
			$kmes .= $key."=".self::formatForSQL($key,$value).", ";
		}
		$kmes = substr($kmes, 0, -2);
		$vmes = substr($vmes, 0, -2);
		
		$con = coreDB();
		mysqli_query($con, $message . $kmes . " WHERE ID=".$this->ID);
		if(mysqli_errno($con)) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "\n" . $message . $kmes . " WHERE ID=".$this->ID);
		mysqli_close($con);
		
		foreach($args as $key => $value){
			$this->$key = $value;
		}
		
	}

	
	
	
	private static function formatForSQL($key, $value){
		$temp = static::$indexes[$key];
		$types = explode(":", $temp["type"]);
		
		if($types[0] == "boolean") return (($value)?"TRUE":"FALSE");
		if($types[0] == "string") return "'".$value."'";
		if($types[0] == "array") return "'".json_encode($value)."'";
		if($types[0] == "aarray"){
			$value .= "'";
			foreach($value as $k => $v)
				$value .= $k.":".$v."|";
			$value = substr($state, 0, -1);
			$value .= "'";
			return $value;
		}
		if($types[0] == "integer") return $value;
		if($types[0] == "object") return $value->ID;
		
	}
	
	public static function find($array, $limit=10, $order="ID", $ascending=true, $group=""){
		$state = "SELECT ID FROM ".get_called_class()."s ";
		
		if(count($array) > 0){
			$state .= "WHERE ";
			foreach($array as $key => $value){
				if($key == "!"){
					$state .= " ".$value." AND ";
					continue;
				}
				if(!static::$indexes[$key]["searchable"]) throw new Exception("can't search for key:".$key);
				//Error checking start
				self::errorChecking($key, $value, $array);
				//Error Checking End
				//preparing statement
				$state .= $key."=".self::formatForSQL($key,$value)." AND ";
			}
			$state = substr($state,0,-4);
		}

		
		
		if($group != ""){
			$state .= "GROUP BY ".$group." ";
		}

		
		
		if(gettype($limit) == "string") $limit = explode("-", $limit);
		if(gettype($limit) == "array")
			$l = "LIMIT ".$limit[0].", ".$limit[1];
		if(gettype($limit) == "integer")
			$l = "LIMIT 0, ".$limit;
		
		$a = ($ascending)? "ASC": "DESC";
		
		
		//getting indexes (since I don't want to open sql twice in a row)
		$con = coreDB();
		$result = mysqli_query($con, $state."ORDER BY ".$order." ".$a." ".$l);
		if(!$result) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "\n" . $state."ORDER BY ".$order." ".$a." ".$l);
		$ids = array();
		while($row = mysqli_fetch_array($result)){
			$ids[count($ids)] = $row[0];
		}
		mysqli_close($con);
		
		if(count($ids) == 0) return array();

		
		//creating objects
		$obs = array();
		for($i=0;$i<count($ids);$i++){
			$class = get_called_class();
			$obs[$i] = new $class($ids[$i]);
		}
		
		return $obs;

		
	}
}

?>