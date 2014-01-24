<?php

include_once( dirname(__FILE__)."/../../genericfunk.php");
include_once( dirname(__FILE__)."/../../generic_classes/sqlobject.php");

if(!class_exists("User")){
class User extends SqlObject{
	
	protected static $indexes = array(
		"ID"=>array("searchable"=>true,"type"=>"integer","default"=>"++","primary"=>true),
		"email"=>array("searchable"=>true,"type"=>"string:email", "not null"=>true, "unique"=>true),
		"nickname"=>array("searchable"=>true, "type"=>"string:rand", "default"=>"Generic User"),
		"permissions"=>array("searchable"=>true, "type"=>"array", "default"=>array()),
		"loggedin"=>array("searchable"=>true,"type"=>"boolean", "default"=>false),
		"timezone"=>array("searchable"=>true,"type"=>"string", "not null"=>true),
		"assertion"=>array("searchable"=>true,"type"=>"string", "not null"=>true),
		"time"=>array("searchable"=>true,"type"=>"integer", "not null"=>true)
		//salt
	);
	
	public static function getDefaultData(){
		return self::getFromSession();
	}
	protected static function getDefaultApp(){ return "none"; }
	protected function pre_construct(&$args){
		$args["time"] = time();
	}
	protected function post_construct(&$args){
	}
	protected function post_get(){
		if($this->loggedin && $this->time > time() + 60*10) $this->set("loggedin", false);
	}
	
	public static function getFromSession(){
		session_start();

		if(isset($_SESSION["user"])){
			try{
				$retuser = new User($_SESSION["user"]);
				if(!$retuser->loggedin){
					self::logout();
					session_destroy();
					$_SESSION  = array();
					return null;		
				}
				date_default_timezone_set($user->timezone);
				$retuser->set("time", time());
				return $retuser;
			}catch(Exception $e){
//				throw new Exception($e);
//				self::logout();
				session_destroy();
				$_SESSION  = array();
				return null;
			}
		}else{
			self::logout();
			return null;
		}
	}

	public static function login($assertion, $timezone){

		$string = postTo("https://verifier.login.persona.org/verify", 
			array("assertion"=>$assertion, "audience"=>$_SERVER['HTTP_HOST'])
		);

		if(!$string) throw new Exception("Bad post call");
		
		$json = json_decode($string, true);
		if($json["status"] != "okay") throw new Exception("Non exsistant with verification");
		if(($users = self::find(array("email"=>$json["email"]))) == null){
			$user = new User(array("email"=>$json["email"], "assertion"=>$assertion, "timezone"=>$timezone, "loggedin"=>true));
		}else{
			$user = $users[0];
			$user->set("loggedin",true);
		}

		$user->set("time", time());
		if(isset($_SESSION["user"])){
		session_destroy();
		}
		session_start();
		$_SESSION["user"] = $user->ID;
		return $user;
	}
	
	public static function logout(){
		session_start();

		if(isset($_SESSION["user"])){
			$user = new User($_SESSION["user"]);
			$user->set("loggedin",false);
			
			session_destroy();
			$_SESSION = array();
		}
	
	}
	
}
User::init();
$global_user = User::getFromSession();

}
?>