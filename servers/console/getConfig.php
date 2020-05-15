<?php
if($sid != -1) {
	require($_SERVER['DOCUMENT_ROOT']."/inc/db.php");
	$sql = "SELECT * FROM servers WHERE id='$sid'";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	define("SERVER_NAME", $row['name']);
	define("SERVER_IP", ($row['ip'] == $_SERVER['SERVER_ADDR'] ? "127.0.0.1" : $row['ip']));
	define("SCREEN_NAME", $row['screen_name']);
	define("SERVER_PORT", $row['port']);
	define("MAX_MIN_RAM", $row['maxMinRam']);
	define("MAX_MAX_RAM", $row['maxMaxRam']);
	define("START_CUSTOMS", $row['startCustoms']);
	define("SERVER_ROOT_DIR", $row['server_root_dir']);
	define("SERVER_LOG_DIR", SERVER_ROOT_DIR . $row['server_log_dir']);
	define("REGEX_FILTERS_LINE", $row['regex_filters_line']);
	define("REGEX_FILTERS_CENSOR", $row['regex_filters_censor']);
	define("IS_MODDED", $row['modded']);
}
define("BUILD_ROOT_DIR", "/home/xelada/minecraft/build/");