<?php

require_once("classes.php");

if($_GET["action"]){
	if($_GET["action"] == "clockin"){
		
	}else if($_GET["action"] == "clockout"){
	
	}

}else{
	if($wb->currentClockIn == null){
?>
		<div id="clockin" class="dev">
		<Form method="GET" action="<?php echo $url; ?>/app.php">
		<input type="hidden" name="action" value="clockin" />
		
		<ul class="horizontal" style="width:500px;"><?php
		if($wb->can_edit == "*"){
			$recent_cls = ClockIn::find(array(), 10, "stop_time", false);
			$dps = array();
			
			if(count($recent_cls) == 0) echo "<h1>Seems like theres no projects worked on recently</h1>";
			else
			foreach($recent_cls as $rcl){
				$project = new DevProject($rcl->project);
				?><li><a class="project" style="width:100px;height:100px;display:inline-block;" href="#"><h2><?php echo $project->name; ?><br/><input type="radio" name="projectID" value="<?php echo $project->ID; ?>"></h2></a></li><?php
				
			}
			
		}else{
			foreach($wb->can_edit as  $p_id){
				$project = DevProject.find($p_id);
				?><li><a class="project" style="width:100px;height:100px;display:inline-block;" href="#"><h2 ><?php echo $project->name; ?><br/><input type="radio" name="projectID" value="<?php echo $project->ID; ?>"></h2></a></li><?php
			}
		}
		?></ul>
<?php
	}else{ 
?>
		<Form method="GET" action="<?php echo $url; ?>/app.php">
		<input type="hidden" name="action" value="clockout" />
		<input type="submit" value="Clock Out" />
		</form>
<?php
	}

}
?>