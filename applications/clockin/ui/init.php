<?php
include_once(dirname(__FILE__)."/../classes/classes.php");
include dirname(__FILE__)."/clocking.php";
include dirname(__FILE__)."/project_list.php";


$url = getUrl(__FILE__);

if($global_user == null) $role = "none";
else{
	$wb = WorkerBoot::findByUserID($global_user->ID);
	$role = $wb->role;
}
?>

<div class="window">
<h1>See recent projects progress!</h1> 
<?php
$current = ClockIn::find(array(),10, "start_time", false, "project");
echo"asdasdasdsadasd";
ProjectList($current);
?>
</div>
<div class="window">
<h1>Developers Currently Clocked In</h1>
<?php

$current = 	ClockIn::find(array("stop_time" => -1), 10, "start_time", true, "user");
ProjectList($current);

?>
</div>

<?php

clocking(null);

?>