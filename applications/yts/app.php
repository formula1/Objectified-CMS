<?php
/*

The way it works
-a person enters the stream
	-we get the last time the stream has been updated
	-Get the difference in time in seconds
	-Set the new current time = this
	-return the appropiate time the video should be loaded at





*/

if(empty($_GET)){
	include "ui.php";
}else{
		include "update.php";
	if($_GET['action'] == "current"){
		echo getcur();
	}

	if($_GET['action'] == "update"){
		updateInfo();
	}
}
?>
