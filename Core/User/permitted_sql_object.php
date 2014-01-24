<?php


abstract class Permitted_Object{

	abstract public static function getPermittedProperties();
	abstract public static function getPermittedMethods();
	
	public static isAllowed($method, $user = $global_user){
		$ourclass = get_called_class();
		if(!in_array($method,get_class_methods($ourclass))) throw new Exception("method doesn't exist");
		$prop = new ReflectionProperty($ourclass, $method);
		if(!$prop->isStatic()) throw new Exception("Can't call this method from static context");

		if($user->permissions != array("*") && !in_array($ourclass, $user->permissions)){
			if( ($permethods = $ourclass::getPermittedMethods()) == null) return false; //not permitted
			else if(!in_array($method, $permthods)) return false; // not permitted
		}
		return true;
	}
	
	protected static function sqlSelection(){
		if(isAllowed("__construct")) return "*";
		$perms = get_called_class()::getPermittedProperties();
		if($perms == null) return null;
		if($perms == array()) return null;
		if(typeof($perms) == "array") return implode(","$perms);
		if(typeof($perms) == "string") return $perms;
		else throw new Exception("improper return type for permitted properties");
	}
	
	public function __construct(){
		$args = func_get_args();
		if(count($args) == 0) throw new Exception("Cannot Create Empty Object of Type:".get_class());
		if(count($args) > 1) throw new Exception("need either an array or id of this class:".get_called_class());
		$args = $args[0];
		if(is_numeric($args)) $args = intval($args);
		
		if(gettype($args) == "integer"){
			if(static::sqlSelection() == null || static::sqlSelection() == "") return;
			parent::__construct($args);
		}else if(self::isAllowed("__construct")){
			parent::__construct($args);
		}
	}
	
}
?>