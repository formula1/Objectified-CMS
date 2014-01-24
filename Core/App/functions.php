<?php


function getAppList(){
	$handle = opendir(__ROOT__.'applications');
	$app_array = array();
	while (false !== ($file = readdir($handle))) { //start folder loop
		if ($file != "." && $file != ".." && is_dir(__ROOT__.'applications/'.$file)) {
			if(file_exists(__ROOT__."applications/".$file."/info.json")){
				$info = json_decode(readfile(__ROOT__."applications/".$file."/info.json"), true);
				$pic = "/applications/".$file."/".$info["piclocation"];
				$title = $info["title"];
				$initurl = $info["url"];
			}else{
				$pic = (file_exists(__ROOT__."applications/".$file."/icon.png"))?"/applications/".$file."/icon.png":"/theme/app.png";
				$title = $file;
				if(file_exists(__ROOT__."applications/".$file."/app.php")) $initurl = "/applications/".$file."/app.php";
				else $initurl = "/applications/".$file;
			}
			$app_array[$title] = array(
					"title"=>$title, 
					"pic"=>$pic, 
					"init_url"=>$initurl, 
					"dir_url"=>$_SERVER['SERVER_NAME']."/applications/".$file
			);
		}
	}
	return $app_array;
}
?>