<?php

global $global_user;
$dir = dirname(__FILE__);
include_once $dir."/../classes/classes.php";

$wb = null;

if($_GET["action"] == "in"){
if($global_user == null) $role = "none";
else{
	$wb = WorkerBoot::findByUserID($global_user->ID);
}

//$clocked = new ClockIn(array("project"=>, "user"=>$wb->ID, "start_time"=>time()));
$wb->ClockIn($_GET["id"]);

$project = intval($_GET["id"]);

clocking(new DevProject($project));


}else if($_GET["action"] == "out"){
if($global_user == null) $role = "none";
else{
	$wb = WorkerBoot::findByUserID($global_user->ID);
}


$clockin = new ClockIn($wb->current_clockin);
$project = $clockin->project;

$wb->ClockOut();
clocking(new DevProject($project));
}




function clocking($project){
$url = getUrl(__FILE__);

global $global_user;

if($global_user == null) $role = "none";
else{
	$wb = WorkerBoot::findByUserID($global_user->ID);
	$role = $wb->role;
}

?>
<div id="clocking">
<?php

if($role != "none" && $wb->current_clockin != null){ ?>
<a class="DevClocked clickable" href="<?php echo $url ?>clocking.php?action=out">Clock Out</a>
<?php 
}else if($role != "none" && ($wb->can_edit == array("*") || in_array($project->ID, $wb->can_edit))){
if($project != null){
?>
<a class="DevClocked clickable" href="<?php echo $url ?>clocking.php?action=in&id=<?php echo $project->ID; ?>">Clock In</a>
<?php
}
?>
<a class="DevClocked clickable" href="<?php echo $url ?>newproject.php">New Project</a>
<?php
}
?>
<a class="DevClocked clickable" href="<?php echo $url ?>project-browse.php">Browse Projects</a>

<script type="text/javascript" >
<?php
if($wb != null && $wb->current_clockin != null){

	$cl = new ClockIn($wb->current_clockin);

?>
var workboo = 0;
function workcheck(){
	workboo++;
	jQuery.ajax("<?php echo getURL($cl->listener_file); ?>mc<?php echo $cl->user; ?>.php").done(function(content){
		workboo--;
		console.log(content);
		if(workboo == 0) setTimeout(workcheck(),100);
	});
}

workcheck();
<?php
}else{ ?>
workboo = false;
<?php
}
?>
</script>
</div>
<?php
}