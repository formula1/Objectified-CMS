<?php

function pie($radius, $clockins){

		$im=imagecreatetruecolor($radius*2, $radius*2);

		imagesavealpha( $im, true );
		$rgb = imagecolorallocatealpha( $im, 0, 0, 0, 127 );
		imagefill( $im, 0, 0, $rgb );
		$pi = imagecolorallocatealpha($im, 0x00, 0x00, 0xFF, 63);

		imagefilledellipse($im, $radius, $radius, $radius*2, $radius*2, $pi);
		$pie = imagecolorallocatealpha($im, 0xFF, 0x77, 0x00, 63);

		
		foreach($clockins as $cl){
		/*
		need to scale each difference by to clock
		*/
			
			$stopped = ($cl->stop_time == -1)?time():$cl->stop_time;
			$degreestart = round(360*($cl->start_time - $ds)/(24*60*60));
			$degreeend = round(360*($stopped - $ds)/(24*60*60));
			
			if($degreestart == $degreeend) $degreeend++;
			
			imagefilledarc($im, $radius-1, $radius-1, $radius*2, $radius*2, -90+$degreestart, -90+$degreeend,  $pie, IMG_ARC_PIE);

		}
		
		ob_start (); 
		imagepng ($im);
		$image_data = ob_get_contents (); 
		imagedestroy($im);
		ob_end_clean (); 

		return base64_encode ($image_data);	
		


}

?>