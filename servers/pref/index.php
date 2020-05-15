<?php 
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
$sid = $_GET['sid'];
if(!isset($sid))header("Location: ../");

include($_SERVER['DOCUMENT_ROOT'] . "/inc/func.php");
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");
require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require $_SERVER['DOCUMENT_ROOT']."/inc/db.php";

if(!ownServer($con, $sid)) {
	header("Location: ../");
}

// SQL Variables
$sql = "SELECT ownerid, hasaccess, modded FROM servers WHERE id='$sid'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$ownerid = $row['ownerid'];
$usr_id_arr = explode (",", $row['hasaccess']);
$usr_id_arr_ownr = $usr_id_arr;
array_push($usr_id_arr_ownr, $row['ownerid']);
$modded = $row['modded'];

echo SERVER_ROOT_DIR . " " . SCREEN_NAME;

// Variables
$boolean = ['true', 'false'];

if(!$modded) {
	// Start script read file
	$file_handle = fopen(SERVER_ROOT_DIR."/start.sh", "r");
	$line = fgets($file_handle);
	fclose($file_handle);
	//echo "Start script: ".$line;

	preg_match("/[^\/]*\.jar/", $line, $m);
	$serverJar = $m[0];

	preg_match("/-Xms([0-9]+)M/", $line, $m);
	$mRam = $m[1];
	preg_match("/-Xmx([0-9]+)M/", $line, $m);
	$xRam = $m[1];

	$script_options = ["mRam" => $mRam, "xRam" => $xRam, "serverJar" => $serverJar];

	// get all files with .jar in /build/ directory
	$server_jars = array();
	foreach(glob(BUILD_ROOT_DIR."*.jar") as $file) {
		$file = basename($file, ".jar");
		if($file == "BuildTools")continue;
		$server_jars[] = $file;
	}
}
if(isset($_POST['deletewf'])) {
	del_worlds(SERVER_ROOT_DIR);
}
if(isset($_POST['submitAddUser'])) {
	$sql = "SELECT username FROM users WHERE id='$_POST[addUser]'"; // check if it's a valid user
	$result = mysqli_query($con, $sql);
	$rows = mysqli_num_rows($result);
	if($rows !== 1) {
		header("Refresh:0");
	} else {
		foreach($usr_id_arr_ownr as $usr_id) {
			if($usr_id == $_POST['addUser'])header("Refresh:0");
		}
		$new = $usr_id_arr;
		array_push($new, $_POST['addUser']);
		$new = implode(', ', $new);
		$sql = "UPDATE servers SET hasaccess='$new' WHERE id='$sid'";
		$result = mysqli_query($con, $sql);
	}
	refresh();
} else if(isset($_POST['submitRmvUser'])) {
	if($_POST['rmvUser'] == $ownerid)header("Refresh:0");
	$sql = "SELECT username FROM users WHERE id='$_POST[rmvUser]'"; // check if it's a valid user
	$result = mysqli_query($con, $sql);
	$rows = mysqli_num_rows($result);
	if($rows !== 1) {
		header("Refresh:0");
	} else {
		$found = false;
		foreach($usr_id_arr_ownr as $usr_id) {
			if($usr_id == $_POST['rmvUser'])$found = true; break;
		}
		if($found !== true)header("Refresh:0");
		$new = $usr_id_arr;
		unset($new[array_search($_POST['rmvUser'], $new)]);
		$new = implode(', ', $new); 
		$sql = "UPDATE servers SET hasaccess='$new' WHERE id='$sid'";
		$result = mysqli_query($con, $sql);
	}
	return refresh();
} else if(isset($_POST['addIconSubmit'])) {
	echo uploadFile(SERVER_ROOT_DIR, "iconUpload", "ico, png, jpeg", "server-icon");
	return refresh();
} else if(isset($_POST['removeIconSubmit'])) {
	$servericons = glob(SERVER_ROOT_DIR."/server-icon.*");
	if(!empty($servericons)) {
		foreach($servericons as $si) {
			unlink($si);
		}
	} else
		echo "No server icons found";
	return refresh();
} else if(isset($_POST['submit']) && !$modded) {
	unset($_POST['submit']);
	if(!empty(array_diff($_POST, $script_options)) || !empty(array_diff($script_options, $_POST))) { // start.sh setting, if at least one POST is a new option
		$f = SERVER_ROOT_DIR."/start.sh";
		$start_script_format = "java -jar -Xms%mRam%M -Xmx%xRam%M %serverJar%.jar";
		$newline = preg_replace(array("/\%serverJar\%/", "/\%mRam\%/", "/\%xRam\%/"),array(BUILD_ROOT_DIR.$_POST['serverJar'], $_POST['mRam'], $_POST['xRam']),$start_script_format);
		// write back to file
		file_put_contents($f, $newline);
		console("Updating database records...");
		$sql = "UPDATE `servers` SET `server-jar`='".$_POST['serverJar']."', `mRam`='".$mRam."', `xRam`='".$xRam."' WHERE `id`='$sid'";
		$result = mysqli_query($con, $sql);
	}
	refresh();
}

require $_SERVER['DOCUMENT_ROOT']."/inc/header.php";
?>
<title>Preferences for <?=SERVER_NAME?></title>
<div class="container">
	<h5 class="text-center">Editing <?=SERVER_NAME?></h5>
	
	<div class="container">
		<div class="row iframe-div" style="height:500px;-webkit-overflow-scrolling:touch">
			<iframe frameborder="0" width="100%" height="200px" src="/servers/console/index.php?sid=<?=$sid?>" style="height:500px;border-radius:10px;overflow-wrap: break-word;"></iframe>
		</div>
	</div>
	
	<hr>
	
	<form method="post">
		<div class="container">
			<div class="row">
				<div class="col-6">
					<table class="table table-striped">
						<tbody>
							<tr class="text-center">
								<th>Has access to this panel:</th>
							</tr>
								<?php
								foreach($usr_id_arr_ownr as $usr_id) {
									if($usr_id == null)continue;
									$sql = "SELECT username FROM users WHERE id='$usr_id'";
									$result = mysqli_query($con, $sql);
									$row = mysqli_fetch_assoc($result);
									echo "<tr><td>$row[username]</td></tr>";
								}
								?>
						</tbody>
					</table>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label for="addUser">Add User</label>
						<select id="addUser" name="addUser" class="form-control">
							<?php
							$sql = "SELECT id, username FROM users";
							$result = mysqli_query($con, $sql);
							while($row = mysqli_fetch_assoc($result)) {
								if(!in_array($row['id'], $usr_id_arr_ownr) && $row['id'] != 1) {
									echo "<option value='$row[id]'>$row[username]</option>";
								}
							}
							?>
						</select>
					</div>
					<div class="col text-center">
						<input class="btn btn-primary text-center" type="submit" value="Add User" name="submitAddUser">
					</div>
					
					<div class="form-group">
						<label for="rmvUser">Remove User</label>
						<select id="rmvUser" name="rmvUser" class="form-control">
							<?php
								foreach($usr_id_arr_ownr as $usr_id) {
									if($usr_id == NULL)continue;
									$sql = "SELECT username FROM users WHERE id='$usr_id'";
									$result = mysqli_query($con, $sql);
									$row = mysqli_fetch_assoc($result);
									echo "<option ";
									if($usr_id == $ownerid)echo 'disabled ';
									echo "value='$usr_id'>$row[username]</option>";
								}
							?>
						</select>
					</div>
					<div class="col text-center">
						<input class="btn btn-primary text-center" type="submit" value="Remove User" name="submitRmvUser">
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<hr>
	
	<div class="row">
		<div class="col-4">
			<form method="post" enctype="multipart/form-data">
				<div class="form-group">
					Upload server-icon (64x64)
					<div class="custom-file">
						<input class="custom-file-input" type="file" name="iconUpload" id="iconUpload" />
						<label class="custom-file-label" for="iconUpload">Choose an icon file...</label>
					</div>
				</div>
				
				<div class="row">
					<div class="col text-right">
						<input class="btn btn-primary btn-block text-center" type="submit" value="Set" name="addIconSubmit">
					</div>
					<div class="col text-left">
						<input class="btn btn-danger btn-block text-center" type="submit" value="Remove" name="removeIconSubmit">
					</div>
				</div>
			</form>
		</div>
		<?php
		$servericon = glob(SERVER_ROOT_DIR."/server-icon.*");
		if(!empty($servericon)) {
			$img = basename($servericon[0]);
			?>
			<div class="col-2 text-center my-auto">
				<img src="/image.php?dir=<?=SERVER_ROOT_DIR?>&img=<?=$img?>" style="width:64px;height:64px;border:1px solid black">
			</div>
		<?php } ?>
	</div>
	
	<hr>
	<br />
	
	<div class="row">
		<div class="col">
			<a class="btn btn-info btn-block" href="plugins/?sid=<?=$sid?>"><i class="fas fa-tools"></i> Plugins</a>
		</div>
		<div class="col">
			<a class="btn btn-info btn-block" href="settings/?sid=<?=$sid?>"><i class="fas fa-cogs"></i> Settings</a>
		</div>
		<div class="col">
			<a class="btn btn-info btn-block" href="serverproperties/?sid=<?=$sid?>"><i class="fas fa-sliders-h"></i> Server.properties</a>
		</div>
		<div class="col">
			<a class="btn btn-info btn-block" href="worlds/manager.php?sid=<?=$sid?>"><i class="fas fa-folder-open"></i> Worlds</a>
		</div>
	</div>
	
	<br />
	<hr>
	
	<?php if(!$modded) { ?>
	<form method="post">
		<div class="form-group">
			<label for="start-script">Start Script</label>
			<input disabled id="start-script" value="<?=$line?>" class="form-control" type="text">
		</div>
		
		<!--<div class="form-group">
			<label for="start-script-customs">Start Script Custom Modifiers</label>
			<input id="start-script-customs" name="startCustoms" value="<?=START_CUSTOMS?>" class="form-control" type="text">
		</div>-->
		
		<div class="form-group">
			<label for="mRam">Minimum Ram</label>
			<select id="mRam" name="mRam" class="form-control" aria-describedby="mRamHelp">
				<?php
				for($n=500;$n <= MAX_MIN_RAM ;$n+=500) {
					echo "<option ";
					if($mRam == $n)echo "selected";
					$gn = $n / 1000; // GB format
					echo " value='$n'>".$gn."GB</option>";
				}
				?>
			</select>
			<small id="mRamHelp" class="form-text text-muted">Suggested: Keep this value as low as possible.</small>
		</div>

		<div class="form-group">
			<label for="xRam">Maximum Ram</label>
			<select id="xRam" name="xRam" class="form-control">
				<?php
				for($n=500;$n <= MAX_MAX_RAM ;$n+=500) {
					echo "<option ";
					if($xRam == $n)echo "selected";
					$gn = $n / 1000; // GB format
					echo " value='$n'>".$gn."GB</option>";
				}
				?>
			</select>
			<small id="xRamHelp" class="form-text text-muted">Suggested: Dedicate about ~150MB of RAM for each player.</small>
		</div>
		
		<div class="form-group">
			<label for="serverJar">Server Jar</label>
			<select id="serverJar" name="serverJar" class="form-control">
				<?php
				foreach($server_jars as $key => $serverJarOpt) {
					echo "<option ";
					if($serverJarOpt.".jar" == $serverJar)echo "selected";
					echo " value='$serverJarOpt'>$serverJarOpt</option>";
				}
				?>
			</select>
		</div>
		
		<div class="col text-center">
			<input class="btn btn-primary text-center" type="submit" value="Edit Preferences" name="submit">
		</div>
	</form>
	<hr>
	<?php } ?> <!-- not modded -->
	<div class="container">
		<div class="row">
			<div class="col-6 text-right">
				<form action="download.php" method="POST">
					<input type="hidden" name="name" value="<?=SERVER_NAME?>'s Worlds" />
					<input type="hidden" name="server_root" value="<?=SERVER_ROOT_DIR?>" />
					<input class="btn btn-info text-center" type="submit" name="clone_submit" value="Download World Files" />
				</form>
			</div>
			<div class="col-6 text-left">
				<form method="POST" onsubmit="return confirm('Are you sure you want to delete this world?');">
					<input class="btn btn-danger text-center" type="submit" value="Delete World Files" name="deletewf" />
				</form>
			</div>
		</div>
	</div>
</div>

<script>
// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>