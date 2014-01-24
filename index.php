<?php

/*
first prepare all the applications' information
*/

include "genericfunk.php";

include "Core/User/user_object.php";

User::init();


?>

<!DOCTYPE html!>
<html>
<head>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="stylesheet" type="text/css" href="/generic_classes/generics.css" />
<script type="text/javascript" src="/generic_classes/jquery.js" > </script>
<script type="text/javascript" src="/Core/App/applictaion-manager-js.php" > </script>
<?php include "theme/head.php"; ?>
</head>
<body style="position:relative;margin:0px">
<?php include "theme/body.php"; ?>

<?php include "Core/User/loginUI.php"; ?>
</body>
</html>