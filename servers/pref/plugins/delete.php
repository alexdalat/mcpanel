<?php
$sid = $_POST['sid'];
if(!isset($sid))header("Location: ../");
require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");
$file = $_POST["File"];
unlink(SERVER_ROOT_DIR."/plugins/WorldEdit/schematics/".$file);
echo $file." deleted.";