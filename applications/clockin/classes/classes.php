<?php


//require_once(dirname(__FILE__)."/../../Core/User/user_object.php");
require_once(dirname(__FILE__)."/../../../generic_classes/sqlobject.php");


class ClockIn extends SqlObject{

	protected static $indexes = array(
						"project"=>array("searchable"=>true,"type"=>"integer:DevProject", "not null"=>true),
						"user"=>array("searchable"=>true, "type"=>"integer:WorkerBoot", "not null"=>true),
						"start_time"=>array("searchable"=>true,"type"=>"integer"),
						"update_time"=>array("searchable"=>false,"type"=>"integer"),
						"stop_time"=>array("searchable"=>true, "type"=>"integer", "default"=>-1)
					);
	
	protected static function getDefaultApp(){ return "none"; }
	public static function getDefaultData(){
		return null;
	}
	
	protected function pre_construct(&$args){
		$con = coreDB();
		$result = mysqli_query($con, "SELECT ID FROM ClockIns WHERE stop_time=-1 AND user=".$args["user"]);
		if(!$result) throw new Exception(mysqli_errno($con) . ": " . mysqli_error($con) . "<br />" ."SELECT FROM ClockIns WHERE stop_time=-1 AND user=".$args["user"]);
		if(null != mysqli_fetch_array($result, MYSQL_ASSOC)){
			throw new Exception("this user:".$args->user."is already clocked in");
		}
		mysqli_close($con);
		$args["update_time"] = time();
	}
	

	protected function post_construct(&$args){
		$con = coreDB();
		mysqli_query($con, "UPDATE WorkerBoots SET current_clockin=".$this->ID." WHERE ID=".$this->user);
		$this->listener_file = dirname(__FILE__).'/../mcs/mc'.$this->user.'.php';
		$filed = dirname(__FILE__).'/../modifiercheck.php';
		copy($filed,$this->listener_file);
		mysqli_close($con);
	}
	
	protected function post_get(){
			$this->listener_file = dirname(__FILE__).'/../mcs/mc'.$this->user.'.php';
	}

	function Stop($timestamp){
		unlink(dirname(__FILE__).'/../mcs/mc'.$this->user.'.php');
		$con = coreDB();
		mysqli_query($con, "UPDATE ClockIns SET stop_time=".$timestamp." WHERE ID=".$this->ID);
		mysqli_query($con, "UPDATE WorkerBoots SET current_clockin=null WHERE ID=".$this->user);
		mysqli_close($con);
	}

}



class DevWork extends SqlObject{
	protected static $indexes = array(
		"ID"=>array("searchable"=>true,"type"=>"integer","default"=>"++","primary"=>true),
		"type"=>array("searchable"=>true,"type"=>"string:enum:create|save|delete", "not null"=>true),
		"time"=>array("searchable"=>true, "type"=>"integer", "not null"=>true),
		"file"=>array("searchable"=>true,"type"=>"string", "not_null"=>true),
		"clockin"=>array("searchable"=>true, "type"=>"integer:ClockIn", "not null"=>true)
	);
	
	public static function getDefaultData(){
		return null;
	}
	protected static function getDefaultApp(){ return "none"; }

	protected function pre_construct(&$args){
	}
	protected function post_construct(&$args){
		$cl = new ClockIn($args["clockin"]);
		$proj = new DevProject($cl->project);
		$files = $proj->files;
		print_r($files);
		if($args["type"] == "delete"){
			$key = array_search($files, $args["file"]);
			array_splice($files, $key, 1);
		}else if($args["type"] == "create"){
			array_push($files, $args["file"]);
		}
		
		$proj->set("files", $files);
			
	}
	protected function post_get(){
	}

}


class DevProject extends SqlObject{
	protected static $indexes = array(
		"ID"=>array("searchable"=>true,"type"=>"integer","default"=>"++","primary"=>true),
		"name"=>array("searchable"=>true,"type"=>"string", "unqiue"=>true, "not null"=>true),
		"root"=>array("searchable"=>true,"type"=>"string", "unqiue"=>true, "not null"=>true),
		"files"=>array("searchable"=>false, "type"=>"array", "not null"=>true)
	);
	public static function getDefaultData(){
		$clockins = ClockIn::find(array(),10,"start_time",false,"project");
		$projects = array();
		foreach($clockins as $cl){
			array_push($projects, new DevProject($cl->project));
		}
		return $projects;
	}
	protected static function getDefaultApp(){ return "clockin"; }


	protected function pre_construct(&$args){
		if(!isset($args["files"])){
			$file = $args["root"];
			if(!file_exists($file)) throw new Exception("This Path does not Exsist:".$args["root"]);
			if(!is_dir($file)) throw new Exception("this Path needs to lead to a Directory");
			function recDir($path){
				if(($file == '.' || $file == '..')){
					continue;
				}
				$path .= "/";
				$files = array();
				$dirHandle = opendir($path);
				while($file = readdir($dirHandle)){
					if(($file == '.' || $file == '..')){
						continue;
					}

					$files[count($files)] = $path.$file;
					if(is_dir($path.$file)){
						$files = array_merge($files, recDir($path.$file));
					}
				}
				return $files;
			}
			$args["files"] = recDir($args["root"]);
		}
	}
	protected function post_construct(&$args){
	}
	protected function post_get(){
	}

}

class WorkerBoot extends SqlObject{

	protected static $indexes = array(
		"ID"=>array("searchable"=>true,"type"=>"integer","default"=>"++","primary"=>true),
		"user"=>array("searchable"=>true,"type"=>"integer:User", "not null"=>true, "unique"=>true),
		"role"=>array("searchable"=>true, "type"=>"string:enum:none|dev|mgmt", "default"=>"none"),
		"can_edit"=>array("searchable"=>false, "type"=>"array", "default"=>array()),
		"current_clockin"=>array("searchable"=>true, "type"=>"integer:ClockIn"),
	);
	
	public static function getDefaultData(){
		$clockins = ClockIn::find(array(),10,"start_time",false,"user");
		$wbs = array();
		foreach($clockins as $cl){
			array_push($wbs, new WorkerBoot($cl->user));
		}
		return $wbs;
	}

	protected static function getDefaultApp(){ return "clockin"; }

	protected function pre_construct(&$args){
	}
	protected function post_construct(&$args){
	}
	protected function post_get(){
	}

	
	public static function findByUserID($id=null){
		if($id == null) $id = $global_user->ID;
		$ari = self::find(array("user"=>$id));
		if(count($ari) == 0) return new WorkerBoot(array("user"=>$id));
		else return $ari[0];
	}
	
	public function ClockIn($project, $timestamp=null){
		if($timestamp == null)  $timestamp = time();
		if(!($project instanceOf DevProject)){
			$project = new DevProject($project);
		}
		if( $this->can_edit == array("*") || in_array($project->ID, $CanEdit) ) $this->current_clockin = new ClockIn(array("user"=>$this->ID, "project"=>$project->ID, "start_time"=>$timestamp));
		else throw new Exception("User:".$this->ID." can't Edit Project:".$project->ID);
	}	
	public function ClockOut($timestamp=null){
		if($timestamp == null) $timestamp =time();
		$temp = new ClockIn($this->current_clockin);
		$temp->Stop($timestamp);
		$this->current_clockin == null;
	}

	public function setCanEdit($project, $boo){
		if($this->role == "none") throw new Exception("Can't Edit when your not a Dev");
		if($this->can_edit == array("*")) return;
		if(!($project instanceOf DevProject)){
			$project = DevProject.find($project);
		}
		if($boo){
			if(!in_array($project->ID, $this->can_edit))$this->can_edit[count($array)] = $project->ID;
			else{
				mysqli_close($con);
				throw new Exception("User:".$this->ID." already can edit Project:".$project->ID);
			}
		}
		if(!$boo){
			if($key = array_search($project->ID, $this->can_edit)) unset($this->can_edit[$key]);
			else {
				mysqli_close($con);
				throw new Exception("User:".$this->ID." can't Edit Project:".$project->ID);
			}
		}

		$con = coreDB();
		msqli_query($con, "UPDATE WorkerBoors SET can_edit='".implode("|", $this->can_edit)."' WHERE wb_id=".$this->ID);
		mysqli_close($con);

	}
}


if($global_user != null){
	$gwb = WorkerBoot::findByUserID($global_user->ID);
}else 				$gwb = null;

?>