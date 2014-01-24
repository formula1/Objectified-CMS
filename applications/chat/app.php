<?php

$url = getURL(__file__);

function loginform(){
?>
<h1>Please Login to Begin Chatting</h1>
<?php
}

function messageform(){
	global $global_user;
	botmess($global_user->nickname." has joined the chat");
?>
<form action="/applications/chat/newmessage.php" target="function" data-function="ap.apps.chat.clearchat">
<input type="text" name="message" />
<input type="submit" value="Send" />
</form>
<?php
}

if(!$_GET["action"]){
?>
<div id="chat">
<?php
	$f1 = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
	$f2 = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
	$file = $f1.dechex(ip2long($_SERVER["REMOTE_ADDR"])).$f2;
	
	$filed = dirname(__FILE__).'/modifiercheck.php';
	$newfile = dirname(__FILE__).'/public/modifiercheck'.$file.'.php';
	copy($filed,$newfile);
?>
<input type="hidden" name="ssid" value="<?php echo $file ?>" />
<div class="chatholder" >
<?php echo file_get_contents(dirname(__FILE__)."/chat.txt"); ?>
</div><div class="formarea"><?php
if($global_user != null){
 messageform();
}
else loginform();
?></div>
<script type="text/javascript">
<?php echo file_get_contents(dirname(__FILE__)."/app.js"); ?>
</script>
</div>
<?php 
}else{

	if($_GET["action"] == "login"){
		messageform();
	}else if( $_GET["action"] == "logout"){
		botmess($_GET["name"]." has left the chat");
		loginform();
	}else if($_GET["action"] == "leave"){
		unlink(dirname(__FILE__).'/public/modifiercheck'.$_GET["ssid"].'.php');
		if($global_user != null) botmess($global_user->nickname." has left the chat");
	}
}
	
function botmess($mes){
$data = file_get_contents(dirname(__FILE__)."/chat.txt"); //read the file
$convert = explode("<br/>", $data);
if(count($convert) >9) array_shift($convert);
$convert[count($convert)] = '['.date('H:i').'] bot : '.htmlentities($mes);
$newstring = implode("<br/>",$convert);

$fh = fopen(dirname(__FILE__).'/chat.txt', 'w');
fwrite($fh, $newstring);
fclose($fh);

$fh = fopen(dirname(__FILE__).'/time.txt', 'w');
fwrite($fh, microtime(true)*1000);
fclose($fh);
}

$handle = opendir(dirname(__FILE__).'/public');
while (false !== ($file = readdir($handle))){
	if($file != '.' && $file != '..')
		if(time()-fileatime(dirname(__FILE__).'/public/'.$file) > 60){
//			botmess("Someone has left the chat");
			unlink(dirname(__FILE__).'/public/'.$file);
			
		}
}



?>