<?php

function weeklyreport($article, $date){

$counter=0;

	include_once dirname(__FILE__)."/../classes/classes.php";
	//Getting proper date
	
	if($date == null) $date = time();
	$date = DateTime::createFromFormat("U",$date);
	$date->setTimeZone(new DateTimeZone(date_default_timezone_get()));
	$date->setTime(0,0,0);
	$day_of_week = $date->format("w");
	$date->modify(-$day_of_week." day");
	
	$b = $date->format("U");
	$date->modify("+7 day");
	$e = $date->format("U");
//Getting proper clockins
	if(get_class($article) == "WorkerBoot"){
		$clockins = ClockIn::find(array("user"=>intval($article->ID),"!"=> "((start_time BETWEEN ".$b." AND ".$e.") OR (stop_time BETWEEN ".$b." AND ".$e."))" ),100, "start_time", false);
	}else if(get_class($article) == "DevProject"){
		$clockins = ClockIn::find(array("project"=>intval($article->ID),"!"=> "((start_time BETWEEN ".$b." AND ".$e.") OR (stop_time BETWEEN ".$b." AND ".$e."))" ),100, "start_time", false);
	}
	
//Parsing into days
	$days = array();
	foreach($clockins as $cl){
		$key = floor(($cl->start_time - $b)/(24*60*60));
		if($cl->stop_time == "-1"){
			$dz = DateTime::createFromFormat("U",time());
			$dz->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			$end = intval($dz->format("U"));
		}else $end = intval($cl->stop_time);
		
		$key2 = floor(($end - $b)/(24*60*60));
		
		$endsday = ($b+$key2*(24*60*60));

		
		if(!isset($days[$key])){
			$days[$key] = 0;
		}
		if(!isset($days[$key2])){
			$days[$key2] = 0;
		}
		if($key2-$key != 0){
			$days[$key] +=  ($b+(1+$key)*(24*60*60)) - $cl->start_time;
			$days[$key2] += $end - $endsday;
		}else{
			$days[$key] += $end - $cl->start_time;
		}
		$counter++;
	}
	
//creating the image with map
	$im = @imagecreate(490, 240)
		or die("Cannot Initialize new GD image stream");
	
	$background_color = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

	$colors = array(
		imagecolorallocate($im, 0xFF, 0x00, 0x00),
		imagecolorallocate($im, 0xFF, 0x77, 0x00),
		imagecolorallocate($im, 0xFF, 0xFF, 0x00),
		imagecolorallocate($im, 0x00, 0xFF, 0x00),
		imagecolorallocate($im, 0x00, 0x00, 0xFF),
		imagecolorallocate($im, 0xFF, 0x00, 0xFF)
	);
	$doc = new DOMDocument();
	$doc->loadHTML("<map name=\"weeklyreport\"></map>");
	$html = simplexml_import_dom($doc);
	$map = $html->body->map;
	
	foreach($days as $key=>$d){
		$rectnum = 0;
		$dq = $d / 360;
		while($dq > 0){
			if($rectnum == 5 || $dq < 20){
				$height = 240 - $d/360;
			}else $height = 240 - ($rectnum+1)*20;
			
			imagefilledrectangle ( $im , 
				10+$key*70, 240-$rectnum*20,
				60+$key*70, $height,
				$colors[$rectnum]
			);
			$dq -= 20;
			$rectnum++;
			$counter++;
		}
		$area = $map->addChild("area");
		$area->addAttribute("shape","rect");
		$area->addAttribute("coords", (10+$key*70).",".(240).",".(60+$key*70).",".($height));
		
		$h = floor($d/3600);
		$m = round(($d%3600)/60);
		
		$area->addAttribute("title","Total hours: ".$h.":".$m);
	}

	ob_start ();
	imagepng ($im);
	$image_data = ob_get_contents ();
	imagedestroy($im);
	ob_end_clean ();

	$i64 = base64_encode ($image_data);
?>	
<div>
<h1>Weekly Report</h1>
<img src="data:image/png;base64, <?php echo $i64; ?>" usemap="#weeklyreport" />
<?php echo $map->asXML(); ?>
</div>
<?php
}
?>