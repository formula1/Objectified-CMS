<?php

$url = getURL(__FILE__);
include dirname(__FILE__)."/../classes/classes.php";
include dirname(__FILE__)."/pie.php";


$page = (isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"] > 0)?$_GET["page"]:0;
$limit = array(10*$page, 10*$page+9);

if($_GET["action"] == "search"){

$projects = DevProject::find(array("!"=>"'name' LIKE '%".html_entities($_GET["search"])."%'"), $limit);
$title = "Search for \"".$_GET["search"]."\"";
}else{
$clockins = ClockIn::find(array(),$limit,"start_time",false,"project");
$projects = array();
foreach($clockins as $cl){
	array_push($projects, new DevProject($cl->project));
}
$title = "Recent Projects Worked on";

}
$title .= ($page != 0)?" page #: ".$page:"";


?>
<form style="display:inline-block;" action="<?php echo $url; ?>?action=search" method="GET">
	<input name="search" type="text" /> <br/>
	<input type="submit" value="search" />
</form>

<?php if(false){
echo "<a></a>";
} ?><h1><?php echo $title; ?></h1>
<?php if(false){
echo "<a></a>";
} ?>
<ul class="horizontal" style="width:600px;"><?php
$i=0;
foreach($projects as $p){
?><li style="position:relative;width:150px;height:150px;text-align:center;"><?php

	$day = DateTime::createFromFormat(
		"U",
		$clockins[0]->start_time
	);
	$day->setTimeZone(new DateTimeZone(date_default_timezone_get()));
	$day->setTime(0,0,0);
	$ds = $day->format("U");
	$day->modify('+1 day');
	$de = $day->format("U");
	
//		$date = new DateTime();
//		$date->modify
	$se = array(
		"project"	=>$p->ID,
		"!"			=>"((start_time BETWEEN ".$ds." AND ".$de.") OR (stop_time BETWEEN ".$ds." AND ".$de."))"
	);

	$cls = ClockIn::find($se,100);
	$base64 = "data:image/jpeg;base64, ".pie(75,$cls);

?>
<a href="<?php echo $p->getURL(); ?>.html"><img src="<?php echo $base64; ?>" style="position:absolute;top:0px;left:0px;z-index:0;" /></a>
<h2><a href="<?php echo $p->getURL(); ?>.html"><?php echo $p->name; ?></a></h2>
<p>Last Clockin:<?php echo $day->format("m/d/Y"); ?></p>
</li><?php
array_shift ($clockins);
}
?></ul>