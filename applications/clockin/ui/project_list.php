
<?php
function ProjectList($cls){

?>

	<ul class="horizontal" style="width:600px;text-align:center;">
	<?php
		foreach($cls as $ci){
			$project = new DevProject(intval($ci->project));
			$wb = new WorkerBoot(intval($ci->user));
			$realuser = new User(intval($wb->ID));
		
	?><li style="width:150px;text-align:center;border:1px solid #000;">
		<h2><a href="<?php echo $project->getURL().".html"; ?>"><?php echo $project->name; ?></a></h2>
		<h3><a href="<?php echo $wb->getURL().".html"; ?>"><?php echo $realuser->nickname; ?></a></h3>
	</li><?php

		}
	?>
	</ul>


<?php
}	
?>