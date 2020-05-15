<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
$sid = $_GET['sid'];
//if(!isset($sid))header("Location: ../");

include($_SERVER['DOCUMENT_ROOT'] . "/inc/func.php");
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");
require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require $_SERVER['DOCUMENT_ROOT']."/inc/db.php";

if(!ownServer($con, $sid)) {
	header("Location: ../");
}

// SQL Variables
$sql = "SELECT `api-token` FROM servers WHERE id='$sid'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$token = $row['api-token'];

if(isset($_POST['regenerate-token'])) {
	$newtoken = keygen(30);
	$sql = "UPDATE `servers` SET `api-token`='".$newtoken."' WHERE `id`='$sid'";
	$result = mysqli_query($con, $sql);
	refresh();
}

require $_SERVER['DOCUMENT_ROOT']."/inc/header.php";
?>
<div class="container">
	<h5 class="text-center">Editing <?=SERVER_NAME?>'s Settings</h5>
	<a class="btn btn-info" href="../?sid=<?=$sid?>">Back</a>
	<br /><br />
	<form method="POST">
		<div class="col-4">
			<div class="form-group">
				<label for="token">API TOKEN</label>
				<input id="token" value="<?=$token?>" class="form-control" type="text" disabled>
			</div>
		</div>
		<div class="col">
			<input class="btn btn-danger text-center" type="submit" value="Regenerate" name="regenerate-token">
		</div>
	</form>
</div>