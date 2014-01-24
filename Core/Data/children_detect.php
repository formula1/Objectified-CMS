<?php

class Extended_Object extends SqlObject{
	static $indexes = array(
		"classname"=>array("searchable"=>true,"type"=>"string:128","not null"=>true, "unique"=>true),
		"permission"=>array("searchable"=>true,"type"=>"string:enum:r|rw|rwx|rx|wx|w|x","default"=>"r"),
		"location"=>array("searchable"=>false,"type"=>"string:path", "not null"=>true),
		"default_app"=>array("searchable"=>true,"type"=>"string:appname", "not null"=>true)
//		,"working"=>array("searchable"=>true,"type"=>"boolean", "default"=>true)
	);

	public static function getDefaultData(){
		return null;
	}
	protected static function getDefaultApp(){ return "none"; }
	protected function pre_construct(&$args){}
	protected function post_construct(&$args){}
	protected function post_get(){}
	
	
	public static function find($array, $limit=10, $order="ID", $ascending=true, $group=""){
		$objs = parent::find($array, $limit, $order, $ascending, $group);
		if(count($objs) == 0){
			return null;
//			throw new Exception("can't find that object");
		}else if(count($objs) == 1) return $objs[0];
		else return $objs;
	}
	
}

Extended_Object::init()
?>