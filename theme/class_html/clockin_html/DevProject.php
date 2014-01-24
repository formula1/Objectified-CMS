<?php

$tempFunk = function($DevProj){

include_once(__ROOT__."applications/clockin/ui/clocking.php");
include_once(__ROOT__."applications/clockin/ui/dailyreport.php");
include_once(__ROOT__."applications/clockin/ui/weekly-report.php");
include_once(__ROOT__."applications/clockin/ui/monthchooser.php");

clocking($DevProj);

dailyreport($DevProj, $_GET["time"]);

weeklyreport($DevProj, $_GET["time"]);

monthchooser($DevProj);



};
?>