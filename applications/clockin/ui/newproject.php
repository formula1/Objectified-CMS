<?php

include_once dirname(__FILE__)."/../classes/classes.php";

if($global_user == null) $role = "none";
else{
	$wb = WorkerBoot::findByUserID($global_user->ID);
	$role = $wb->role;
}

if($role != "none"){

	$url = getURL(__FILE__);
	$path = "../../../";

	if($_GET["path"]) $path = urldecode($_GET["path"]);
	

	if($_GET["action"] == "updir") $path = ($path == "../../../")? "../../../":dirname($path)."/";
	else if($_GET["action"] == "newfolder"){
		$path .= $_GET["folder"]."/";
		mkdir(dirname(__FILE__)."/".$path);
	}else if($_GET["action"] == "enter"){
		$path .= $_GET["folder"]."/";
	}else if($_GET["action"] == "doit"){
		$path = cleanPath(dirname(__FILE__)."/".$path);
		$project = new DevProject(array("name"=>$_GET["folder"],"root"=>$path.$_GET["folder"]));
		$project->HTML();
		die();
	};
	$erl = $url. "newproject.php?path=". urlencode ($path);
	?>
	<div class="WorkFileStruc">
	<a href="<?php echo $erl; ?>&action=updir">Go Up a directory</a><br/>
	<form style="display:inline-block;" action="<?php echo $erl; ?>&action=newfolder" method="GET">
		<input name="newfolder" type="text" /> <br/>
		<input type="submit" value="new folder" />
	</form>
	<ul class="horizontal">
	<?php
	if ($handle = opendir(dirname(__FILE__)."/".$path)) {
		/* This is the correct way to loop over the directory. */
		while (false !== ($entry = readdir($handle))) {
			if(!is_dir(dirname(__FILE__)."/".$path.$entry) || ($entry == "." || $entry == "..")){
				continue;
			}
		?>
		<li style="width:150px;height:150px;vertical-align:top;">
			<h3><?php echo $entry; ?></h3>
			<?php
				$pp = cleanPath(dirname(__FILE__)."/".$path.$entry);
				$f = DevProject::find(array("root"=>$pp));
				if(count($f)  == 0){
			?>
			<h4><a href="<?php echo $erl; ?>&action=enter&folder=<?php echo $entry; ?>">Open Directory</a></h4>
			<h4><a href="<?php echo $erl; ?>&action=doit&folder=<?php echo $entry; ?>">Use As Project Repository</a></h4>
			<?php
				}else{?>
				<h4><a href="<?php echo $f[0]->getURL().".html"; ?>">See Project Page</a></h4>
				<?php
				}
			?>
		</li>
	<?php
		}

		closedir($handle);
	}
	?>

	</ul>
	</div>
	<?php
}else{
?>

<h1>this page is specifically for developers and managers</h1>

<?php
}
?>