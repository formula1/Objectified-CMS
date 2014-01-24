<?php

include_once __ROOT__."generic_classes/xml_serializer.php";

class MenuItem{
	public $name;
	public $url;
	public $pic;

	public function __construct($name, $url, $pic){
		$this->name = $name;
		$this->url = $url;
		$this->pic = $pic;
	}

}

function getMenu(){
	$menu_items = array();

include_once __ROOT__."Core/App/functions.php";
	$app_array = getAppList();
	foreach($app_array as $app){
		array_push($menu_items, new MenuItem($app["title"], $app["init_url"], $app["pic"]));
	}

	include_once __ROOT__."Core/Data/children_detect.php";
	$decent = Extended_Object::find(array(),20);
	foreach($decent as $c){
		array_push($menu_items, new MenuItem($c->classname, "/".$c->classname.".html", $app["pic"]));
	}

	return $menu_items;
}

function dealWithData($request, $data=null){
?>
<!DOCTYPE html>
<html>
<head>
<title>Sam Tobias Website</title>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="stylesheet" type="text/css" href="/theme/theme.css" />
<script type="text/javascript" src="/theme/all/jquery.js" ></script>
<script type="text/javascript" src="/theme/all/phytree.js"></script>
<?php include dirname(__FILE__)."/head.php"; ?>
</head>
<body>
<?php include dirname(__FILE__)."/body.php"; ?>

<?php include __ROOT__."Core/User/loginUI.php"; ?>
<script type="text/javascript" src="/theme/ajaxify/frame_creator.js" ></script>
</body>
</html>

<?php
}
?>