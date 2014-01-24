<?php

/*
$dirHandle = opendir("/".dirname(dirname(dirname(__FILE__))));
while($file = readdir($dirHandle)){
	echo $file."<br />";
}



*/

	include dirname(__FILE__)."/../../../genericfunk.php";
	include "../user_object.php";
	
	if($_POST["assertion"]) $assertion = $_POST["assertion"];
	if($assertion == "") throw new Exception("need assertion");
	if($_POST["timezone"]) $timezone = $_POST["timezone"];
	else $timezone = date_default_timezone_get();

	$ret = array("status"=>"1", "reason"=>"");

	try{
		$user = User::login($assertion, $timezone);
	}catch(Exception $e){
		$ret["status"] = -1;
		$ret["reason"] = $e->getMessage();
		die(json_encode($ret));
	}
	$ret["user"] = get_object_vars($user);

	die(json_encode($ret));

?>