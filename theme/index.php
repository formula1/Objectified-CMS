<?php

/*

So....
We have a bunch of data objects
	-for each data object
		-if user can view, execute or edit those objects
			-list data objects they can access
	-On click
		-display static methods they can access
			-find
			-assorted things
		-display a list of those objects based on primary (alternative to ID which is true primary)
			-display object summery they can access
			-On click
				-display content details
				-display object methods they can access


We need user permissions
	-admin-access, execute, write everything
	-worker-access, execute, write on admin permission bases
	-normal user-access, execute, write on object permission basis

Theme is...
	-simply a way to display each aspect
	-but if these aspects are not available, we cannot display them



We then allow the urls to become prettier due to users choice

We then allow the urls to be ajaxed



first prepare all the applications' information



*/


ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Sam Tobias Website</title>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="stylesheet" type="text/css" href="/theme/theme.css" />
<script type="text/javascript" src="/theme/all/jquery.js" > </script>
<script type="text/javascript" src="/theme/fractals/phytree.js"></script>
<script type="text/javascript" src="/Core/App/public/applictaion-manager-js.php" > </script>
<?php include dirname(__FILE__)."/head.php"; ?>
</head>
<body>
<?php include dirname(__FILE__)."/body.php"; ?>

<?php include __ROOT__."Core/User/loginUI.php"; ?>
<script type="text/javascript" src="/theme/ajaxify/frame_creator.js" ></script>
</body>
</html>

<?php
$html = ob_get_contents ();
ob_end_clean ();

include dirname(__FILE__)."/ajaxify/anchor_manipulation.php";
$html = anchor_manip($html);
echo $html;

?>