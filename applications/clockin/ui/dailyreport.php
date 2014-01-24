<?php

function dailyreport($article, $date){
	include_once dirname(__FILE__)."/../classes/classes.php";

	if($date == null) $date = time();
	$date = DateTime::createFromFormat("U",$date);
	$date->setTimeZone(new DateTimeZone(date_default_timezone_get()));
	$date->setTime(0,0,0);
	$b = $date->format("U");
	$date->modify("+1 days");
	$e = $date->format("U");
	

	$doc = new DOMDocument();
	$doc->loadHTML("<div class=\"projects list inline\" style=\"vertical-align:top;padding:10px;border:1px solid #DDD;\"></div>");
	$temp = simplexml_import_dom($doc);
	$phtml = $temp->body->div;

	//Prepare number of lines
	$lines = array();
	
	$title = get_class($article);
	$total_hours = 0;
	if(get_class($article) == "WorkerBoot"){
		$phtml->addChild("h3", "Projects");
		$ul = $phtml->addChild("ul");
		$u = new User(intval($article->user));
		$title = $u->nickname;

		$clockins = ClockIn::find(array("user"=>intval($article->ID),"!"=> "((start_time BETWEEN ".$b." AND ".$e.") OR (stop_time BETWEEN ".$b." AND ".$e."))" ),20, "start_time", false);
		foreach($clockins as $clockin){
			if(array_key_exists($clockin->project, $lines)){
				array_push($lines[$clockin->project], $clockin);
				continue;
			}else{
				$lines[$clockin->project] = array($clockin);
				$project = new DevProject(intval($clockin->project));
				$li = $ul->addChild("li");
				$a = $li->addChild("a", $project->name);
				$a->addAttribute("href",$project->getURL().".html");
			}
		}
	}else if(get_class($article) == "DevProject"){
		$phtml->addChild("h3", "Developers");
		$ul = $phtml->addChild("ul");
		$title = $article->name;

		$clockins = ClockIn::find(array("project"=>intval($article->ID),"!"=> "((start_time BETWEEN ".$b." AND ".$e.") OR (stop_time BETWEEN ".$b." AND ".$e."))" ),20, "start_time", false);
		foreach($clockins as $clockin){
			if(array_key_exists($clockin->user, $lines)){
				array_push($lines[$clockin->user], $clockin);
				continue;
			}else{
				$lines[$clockin->user] = array($clockin);
				$wb = new WorkerBoot($clockin->user);
				$user = new User($wb->user);
				$li = $ul->addChild("li");
				$a = $li->addChild("a", $user->nickname);
				$a->addAttribute("href",$wb->getURL().".html");
			}
		}
	}


	$im = @imagecreate(864/2, max(20,count($lines)*20))
		or die("Cannot Initialize new GD image stream");
	
	$doc = new DOMDocument();
	$doc->loadHTML("<map name=\"dailyreport\"></map>");
	$map = simplexml_import_dom($doc);

	$background_color = imagecolorallocate($im, 0, 0, 0);

	$inactive = imagecolorallocate($im, 0xFF, 0x77, 0xFF);
	$active = imagecolorallocate($im, 0xff, 0xff, 0x77);

	$scolor = imagecolorallocate($im, 0x00, 0xff, 0x00);
	$ecolor = imagecolorallocate($im, 0xff, 0x00, 0x00);
	
	$ccol = imagecolorallocate($im, 0xFF, 0x77, 0x00);
	$scol = imagecolorallocate($im, 0x77, 0x77, 0x77);
	$dcol = imagecolorallocate($im, 0x00, 0x00, 0xFF);

	imagesetthickness($im, 3);


	$offset = 10;
	$num = 0;
	foreach($lines as $line){
		$offset = 10 + $num*20;;
		$num++;
		imageline($im, 0,$offset,864/2,$offset, $inactive);
		foreach($line as $cl){
			$s_mm = max($cl->start_time-$b, 0);
			$slx = $s_mm/200;
			
			imagefilledrectangle ( $im , 
				$slx-4, $offset-4,
				$slx+4 , $offset+4,
				$scolor 
			);
			
			$stopped = ($cl->stop_time != -1)?$cl->stop_time:time();
			$ecol = ($cl->stop_time != -1)?$ecolor:$active;
			$e_mm = min($stopped-$b, 86400);
			
			$elx = $e_mm/200;
			imageline($im,
				$slx,$offset, 
				$elx,$offset, 
				$active
			);

			$total_hours += ($e_mm - $s_mm);
			

			imagefilledrectangle ( $im , 
				$elx-4, $offset-4,
				$elx+4 , $offset+4,
				$ecol 
			);

			$d = DateTime::createFromFormat("U",intval($cl->start_time));
			$d->setTimeZone(new DateTimeZone(date_default_timezone_get()));

			$stf = date_format($d, 'm/d/Y H:i:s');
			
			$mizzle = $map->body->map;
			$area = $mizzle->addChild("area");
			$area->addAttribute("shape","rect");
			$area->addAttribute("coords", ($slx-4).",".($offset-4).",".($slx+4).",".($offset+4));
			$area->addAttribute("title","Started:".$stf);

			$d = DateTime::createFromFormat("U",intval($stopped));
			$d->setTimeZone(new DateTimeZone(date_default_timezone_get()));
			$etf = date_format($d, 'm/d/Y H:i:s');

			
			$area = $mizzle->addChild("area");
			$area->addAttribute("shape","rect");
			$area->addAttribute("coords", ($elx-4).",".($offset-4).",".($elx+4).",".($offset+4));
			if($cl->stop_time != -1){
				$area->addAttribute("title","Ended:".$etf);
			}else{
				$area->addAttribute("title","Still Going:".$etf);
			}
			$works = DevWork::find(array("clockin"=>$cl->ID), 100);
			foreach($works as $w){
				if($w->time-$b < 0 || $w->time-$b > 86400) continue;
			
				$slx = ($w->time-$b)/200;
				if($w->type == "create") $col = $ccol;
				if($w->type == "save") $col = $scol;
				if($w->type == "delete") $col = $dcol;

				
				imagefilledellipse ( $im , 
					$slx, $offset,
					8 , 8,
					$col
				);

				
				$d = DateTime::createFromFormat("U",intval($w->time));
				$d->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				$stf = date_format($d, 'm/d/Y H:i:s');

				$area = $mizzle->addChild("area");
				$area->addAttribute("shape","circle");
				$area->addAttribute("coords", ($slx).",".($offset).","."8");
				$area->addAttribute("title",$w->type.":".$stf);
			}
			
		}
	}
				
		
	ob_start (); 
	imagepng ($im);
	$image_data = ob_get_contents (); 
	imagedestroy($im);
	ob_end_clean (); 

	$i64 = base64_encode ($image_data);	

?>
<div class="dailyreport">
<h2><?php echo $title; ?>'s Daily Report for <?php echo date_format(DateTime::createFromFormat("U",$b,new DateTimeZone(date_default_timezone_get())), 'm/d/Y'); ?></h2>

<?php
	echo $phtml->asXML();
	
	$h =floor($total_hours/3600);
	$m = round(($total_hours%3600)/60);
	
?><div class="image-hold inline" style="vertical-align:top;padding:10px;border:1px solid #DDD;">
<h3>Total hours : <?php echo $h.":".$m; ?></h3>
<img src="data:image/png;base64, <?php echo $i64; ?>" usemap="#dailyreport" />
<?php echo $map->asXML(); ?>
</div>
</div>

<?php


}
?>