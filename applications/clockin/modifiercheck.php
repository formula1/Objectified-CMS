<?php
/*
	project modifier listener
*/

include dirname(__FILE__)."/../classes/classes.php";

if($gwb == null) throw new Exception("no WorkerBoot is available for this user");
try{
	$ClockIn = new ClockIn($gwb->current_clockin);
	if($ClockIn->stop_time != -1) throw new Exception("This Clock has stopped");
}catch(Exception $e){
	die($e);
}
$project = new DevProject($ClockIn->project);
$start_time = time();
$dirs = $project->files;

function recurseDirs($main){
	global $ClockIn;
	global $dirs;
	$main .= "/";
    $dirHandle = opendir($main);
    while($file = readdir($dirHandle)){
		if($file == '.' || $file == '..') continue;
		$boo = false;
		if(($key = array_search($main.$file, $dirs)) !== false)
			array_splice($dirs,$key,1);
		else{
			new DevWork(array("type"=>"create", "time"=>filemtime($main.$file), "file"=>$main.$file, "clockin"=>$ClockIn->ID));
			$boo = true;
		}
		if(is_dir($main.$file))	recurseDirs($main.$file);
		else if(!$boo && $ClockIn->update_time < filemtime($main.$file))	
			new DevWork(array("type"=>"save", "time"=>filemtime($main.$file), "file"=>$main.$file, "clockin"=>$ClockIn->ID));
    }
}
recurseDirs($project->root);
foreach($dirs as $dir) new DevWork(array("type"=>"delete", "time"=>time(), "file"=>$dir, "clockin"=>$ClockIn->ID));

$ClockIn->set("update_time",time());
?>