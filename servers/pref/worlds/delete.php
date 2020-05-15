<?php
$sid = $_POST['sid'];
$file = $_POST["File"];
if(!isset($sid) || !isset($file))header("Location: ../");
require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require($_SERVER['DOCUMENT_ROOT']."/inc/func.php");
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");

$target = SERVER_ROOT_DIR.$file;
if(is_dir($target)) {
	echo del_dir($target);
} else if(file_exists($target)) { # is a file
	echo del_file($target);
} else {
	echo "Delete failure.";
}