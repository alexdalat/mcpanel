<?php
if ($_POST['logstatus']) {
    $status = $_POST['logstatus'];
	$sid = $_POST['sid'];
	include("getConfig.php");
    if ($status == "check")	{
        $check = shell_exec("sudo ls -l " . SERVER_LOG_DIR);
        echo strtok($check, " ");
	  } elseif ($status == "update") {
		    shell_exec("sudo chmod 666 " . SERVER_LOG_DIR);
		    $check = shell_exec("sudo ls -l " . SERVER_LOG_DIR);
        echo strtok($check, " ");
	  }
}

?>
