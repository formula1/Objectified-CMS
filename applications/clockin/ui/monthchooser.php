<?php
include_once(dirname(__FILE__)."/../classes/classes.php");



function monthchooser($article){
include_once(dirname(__FILE__)."/../../../genericfunk.php");
include_once(dirname(__FILE__)."/../../../generic_classes/Calander.php");
include dirname(__FILE__)."/pie.php";

$url = getUrl(__FILE__);

global $temp;
if(get_class($article) == "WorkerBoot"){
	$temp = array(
		"s" => array("user"=>$article->ID),
		"href" => $article->getURL().".html"
	);
}else if(get_class($article) == "DevProject"){
	$temp = array(
		"s" => array("project"=>$article->ID),
		"href" => $article->getURL().".html"
	);
}


class dailyclockins extends CalenderUI{

	public function day_data($string){
		global $temp;
		$day = new DateTime($string);
		$day->setTimeZone(new DateTimeZone(date_default_timezone_get()));
		$ds = $day->format("U");
		$day->modify('+1 day');
		$de = $day->format("U");
		
//		$date = new DateTime();
//		$date->modify
		
		$temp["s"]["!"] = ("((start_time BETWEEN ".$ds." AND ".$de.") OR (stop_time BETWEEN ".$ds." AND ".$de."))" );

		$clockins = ClockIn::find($temp["s"],100);

		$i64 = pie(35, $clockins);	
		
		return '<a class="dailychoice" href="'.$temp["href"].'?time='.$ds.'">
			<img src="data:image/jpeg;base64, '.$i64.'" usemap="#dailyreport" />
		</a>';
	}

};

if(isset($_GET["time"])){
$dizzle = DateTime::createFromFormat("U", $_GET["time"]);
$month = $dizzle->format("m");
$year = $dizzle->format("Y");
}else{
$month = (isset($_GET["month"]))?$_GET["month"]:date("m");
$year = (isset($_GET["year"]))?$_GET["year"]:date("Y");
}

$start = new DateTime('01-'.$month.'-'.$year);

if($month != 1) $before = new DateTime('01-'.($month-1).'-'.($year));
else $before = new DateTime('01-12-'.($year-1));
if($month != 12) $after = new DateTime('01-'.($month+1).'-'.$year);
else $after = new DateTime('01-1-'.($year+1));
?>
<div class="cl_month">
<div class="monthchooser" style="text-align:center;">
	<a class="monthchoice" href="<?php echo $article->getURL().".html?month=".$before->format("m")."&year=".$before->format("Y"); ?>">&#60;&#60;</a><?php
	?><span><?php echo $start->format("F"); ?></span><?php
	?><a class="monthchoice" href="<?php echo $article->getURL().".html?month=".$after->format("m")."&year=".$after->format("Y"); ?>">&#62;&#62;</a>
</div>
<?php 
$cal = new dailyclockins();
echo $cal->get_calender($month,$year);?>
</div>
<?php
}
?>