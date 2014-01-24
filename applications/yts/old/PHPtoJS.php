<?php 

$counter = 1;

while($_GET['loc'.$counter]) {

$var = 1;
$thevars = array();
while($_GET['loc'.$counter.'var'.$var]){
$thevars[$var] = $_GET['loc'.$counter.'var'.$var];
$var += 1;
}

include $_GET['loc'.$counter];


if($_GET['loc'.$counter.'return'] != ""){
$returned = tempfunk($thevars[1], $thevars[2]);
$stringer = '<script type="text/javascript">

function myfunk(){

parent.' . $_GET['loc'.$counter.'return'] . '("' . $returned . '");

}

window.onload=myfunk;
</script>';
echo $stringer;
}

$counter += 1;
}
//echo $_GET['location'];

?>