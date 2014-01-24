<?php

$tempFunk = function($WorkerBoot){

include_once(__ROOT__."applications/clockin/ui/dailyreport.php");
include_once(__ROOT__."applications/clockin/ui/weekly-report.php");
include_once(__ROOT__."applications/clockin/ui/monthchooser.php");

dailyreport($WorkerBoot, $_GET["time"]);

weeklyreport($WorkerBoot, $_GET["time"]);


monthchooser($WorkerBoot);



};


?>