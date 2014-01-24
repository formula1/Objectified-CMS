<?php
$info = find("ytsinfo.json", false);
include dirname(__FILE__)."/../../generic_classes/datetime_52.php";

function find($url, $google=true){
	if($google){
		global $info;
	// https://www.googleapis.com/youtube/v3/subscriptions?part=snippet&channelId=UCCBF4DwlK0_mWO2UlOcI_4w&maxResults=50&key=AIzaSyDaduOKfegCSkNG2XBNknuoreLI9TaOde4
		$url = "https://www.googleapis.com/youtube/v3/".$url."&key=".$info["serverkey"];
	}else
		$url = dirname(__FILE__)."/".$url;
	
	try{
		$handle = fopen($url, "r");
		$manny = stream_get_contents($handle);
		fclose($handle);
		$ret = json_decode($manny, true);
		return $ret;
	}catch(Exception $e){
		echo $url;
		exit(1);
	}
	
}

function updateSingle($channelTitle, $channelID){
	global $info;
	
	
	$con=mysqli_connect("Youtubed.db.5356314.hostedresource.com","Youtubed","s!re4mEd","Youtubed");
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$result = mysqli_query($con,"SELECT * FROM channels WHERE title='".$channelTitle."'");
	if(mysql_num_rows($result) == 0 || 	mysqli_error($con) != ""){
		if(	mysqli_error($con) != "") die(mysqli_error($con));
		mysqli_query($con,"INSERT INTO channels (title, ytid, lastupdated,storedvids) VALUES ('".$channelTitle."', '".$channelID."', 0,0)");
		$result = mysqli_query($con,"SELECT * FROM channels WHERE title='".$channelTitle."'");
	}
	
	
	$channel = mysqli_fetch_array($result);
			
	$url = "http://gdata.youtube.com/feeds/api/users/".$channelID."/uploads?v=2&alt=json";
	$url .= "&max-results=1";
	$url .= "&orderby=published";
	$url .= "&safeSearch=none";
	$url .= "&key=".$info["serverkey"];

	$temp = find($url, false);
	$uploads = $temp["feed"];
	
	//end if we're updated
	$ti = $uploads["entry"][0]["published"]["\$t"];
	$dateObject = new DateTime_52($ti);
	$publishedAt = $dateObject->getTimestamp();
	if($publishedAt < $channel["lastupdated"]){
		mysqli_close($con);
		return 0;
	}
	$totalvideos = $uploads["openSearch\$totalResults"]["\$t"];

	$ci = 1;
	$url = "http://gdata.youtube.com/feeds/api/users/".$channelID."/uploads?v=2&alt=json";
	$url .= "&max-results=50";
	$url .= "&orderby=published";
	$url .= "&safeSearch=none";
	$url .= "&key=".$info["serverkey"];
	$url .= "&start-index=";

	
	
//	$uploads = find("playlistItems?part=contentDetails,snippet&playlistId=".$playlistref."&maxresults=50");
	while($totalvideos > $ci-1 && $ci < 301){
		$temp = find($url.$ci, false);
		$uploads = $temp["feed"];

		foreach($uploads["entry"] as $i){
			$dateObject = new DateTime_52($ti);
			$publishedAt = $dateObject->getTimestamp();
			if($publishedAt < $channel["lastupdated"]) break 2;

			$boo = true;
			foreach($i["yt\$accessControl"] as $ac){
				if($ac["action"] == "embed"){
					$boo = ($ac["permission"] == "allowed");
					break;
				}
			}
			if(!$boo){$ci++; continue;}
			
			$id = $i["media\$group"]["yt\$videoid"]["\$t"];
			$title = $i["title"]["\$t"];
			$length = $i["media\$group"]["yt\$duration"]["seconds"];
			mysqli_query($con,'INSERT INTO videos (ytid, title, length,numplays,owner) VALUES ("'.$id.'","'.htmlentities ($title, ENT_QUOTES).'",'.$length.',0,"'.$channelTitle.'")');
			if(	mysqli_error($con) != ""){
				echo mysql_errno();
				die("# $ci- $id:".mysqli_error($con));
			}

			$ci++;
		}
	}
	
	mysqli_query($con,"UPDATE channels SET storedvids=".($ci-1).", lastupdated=".time()." WHERE title='".$channelTitle."'");

	mysqli_close($con);

	return $ci-1;
	/*
		I want to save each artist
			-name of artist
			-number of videos we have
			-last time we got videos from them
			
		Video Object
			-Artist Reference
			-Name of Video
			-Length of video
			-number of times played - sort to avoid repeats
			
		202 438 9199
		phil@atomicstudios.com
		
	*/
	
	

}


function updateInfo(){
	global $info;

	$totalvideos=0;
	$asked=0;
	$pagetoken = "";
	do{
		$ginfo = find("subscriptions?part=snippet&channelId=".$info["channel"]."&maxResults=50".$pagetoken);
		$counter = count($ginfo["items"]);
		for($n=0;$n<$counter&&$totalvideos<300;$n++){
			$i = $ginfo["items"][$n];
			$totalvideos += updateSingle($i["snippet"]["title"], $i["snippet"]["resourceId"]["channelId"]);
			$asked++;
			echo $totalvideos;
		}
		$pagetoken = "&pageToken=".$ginfo["nextPageToken"];
		
	}while($ginfo["pageinfo"]["totalresults"] > $asked && $totalvideos < 300);

	echo $totalvideos;

}

function updatePlaylist($vids){
	$ask = "SELECT MIN(numplays) as minplays FROM videos";
	$con=mysqli_connect("Youtubed.db.5356314.hostedresource.com","Youtubed","s!re4mEd","Youtubed");
	$result = mysqli_query($con,$ask);
	$temp = mysqli_fetch_array($result);
	$min = $temp["minplays"];

	while(count($vids) < 10){
		$ask = "SELECT *
			FROM videos
			WHERE numplays=".$min."
			ORDER BY RAND()
			LIMIT ".(10-count($vids))
			;
		$result = mysqli_query($con,$ask);
		if(mysqli_error($con)) die(mysqli_error($con));

		$ask = "UPDATE videos SET numplays=numplays+1
		WHERE ";
		$boo = false;
		while($temp = mysqli_fetch_array($result)){
			if($boo) $ask .= " OR ";
			else $boo = true;
			$ask .= "id = ".$temp["id"];
			$vids[count($vids)] = array("title"=>$temp["title"],"author"=>$temp["owner"],"id"=>$temp["ytid"], "vidlength"=>$temp["length"]);
		}
		mysqli_query($con,$ask);
		mysqli_close($con);
		
	}
	
	file_put_contents("playlist.json", json_encode($vids));

	return $vids;
}

function getcur(){
	global $info;
	$vids = find("playlist.json", false);

	if(count($vids) < 10) $vids = updatePlaylist($vids);

	$curtime = time();
	if($info["starttime"] != 0) $diff = $curtime - $info["starttime"];
	else $diff = 0;

	while($diff > $vids[0]["vidlength"]){
		$diff -= $vids[0]["vidlength"];
		array_shift($vids);

		if(count($vids) == 0){
			$vids = updatePlaylist($vids);
			$diff = 0;
			break;
		}
	}
	$vids = updatePlaylist($vids);

	$info["starttime"] = $curtime - $diff;
	file_put_contents("ytsinfo.json", json_encode($info));

	$vids[0]["time"] = $diff;
	return json_encode($vids[0]);
}





?>