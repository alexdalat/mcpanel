<?php
$sid = $_POST['sid'];
include("getConfig.php");
$timeout = 6;

$fsock = fsockopen(SERVER_IP, SERVER_PORT, $errno, $errstr, $timeout);
if(!$fsock) {
    echo "offline";
} else {
    echo "online";
}

?>