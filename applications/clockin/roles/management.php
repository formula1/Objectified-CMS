<?php



?>

<div>
<h1>Add a Dev</h1>

<h1>Currently Clocked In</h1>
<div class="window">
<ul class="horizontal">
<?php
	$current = ClockIn::find(array("stop_time" => -1));
	foreach($current as $ci){
	$project = Project.find($ci->project);
	$wb = WorkerBoot.find($ci->user);
	
	
?><li>
	<h2><a href="<?php echo $url; ?>/app.php?view=project&id=<?php echo $project->ID; ?>"><?php echo $project->name; ?></a></h2>
	<h3><a href="<?php echo $url; ?>/app.php?view=user&id=<?php echo $wb->ID; ?>"><?php echo $realuser->tagname; ?></a></h3>
</li><?php

	}
?>
</ul>
</div>
</div>
