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

// Variables
$boolean = ['true', 'false'];
$difficulties = ['peaceful', 'easy', 'normal', 'hard'];
$gamemodes = ['creative', 'survival', 'adventure', 'spectator'];
$level_types = ['Default', 'Amplified', 'Flat', 'LargeBiomes','Customized'];

// erver.properties read file
$lines = file(SERVER_ROOT_DIR."/server.properties", FILE_IGNORE_NEW_LINES);
$options = [];

$available_options = ["server-port", "difficulty", "gamemode", "view-distance", "motd", "level-type", "enable-command-block", "level-name", "level-seed", "max-players", "max-world-size", "spawn-protection", "generator-settings", "online-mode"];
foreach($available_options as $option) {
	$oldOption = $option; //ex. server-port, view-distance
	$matched = current(preg_grep('/^'.$option.'=/', $lines)); // current gets first from array (only)
	$option = str_replace('-', '_', $option); // prepare to be made a variable
	${$option} = substr($matched, strpos($matched, "=") + 1);
	$options += [$oldOption => ${$option}];
}

if(isset($_POST['submit'])) {
	try {
		unset($_POST['submit']);
		if(!empty(array_diff($options, $_POST)) || !empty(array_diff($_POST, $options))) { // server.properties setting, if at least one POST is a new option
			$lines = file(SERVER_ROOT_DIR."/server.properties", FILE_IGNORE_NEW_LINES);
			// modify
			foreach($_POST as $key=>$value) {
				foreach($lines as $line_num => $line) {
					$val = explode("=",$line, 2);
					if($key == $val[0]) {
						// Validate alphanumeric
						if (!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $value)&&$key!="motd"&&$key!="generator-settings"&&$value!=true&&$value!=false)
							throw new Exception("Only use alphanumeric inputs!");
						$val[1] = $value;
						$lines[$line_num] = implode("=",$val);
					}
				}
			}
			unset($line);

			// save again
			$handle = fopen(SERVER_ROOT_DIR."/server.properties", "w");
			#console("Updating server.properties...");
			foreach($lines as $line) {
				fwrite($handle,$line.PHP_EOL);
			}
			fclose($handle);

			if($_POST['server-port']!=$server_port) {
				console("Updating database records...");
				$sql = "UPDATE `servers` SET `port`='".$_POST['server-port']."' WHERE `id`='$sid'";
				$result = mysqli_query($con, $sql);
			}
		}
	} catch(Exception $e) {
		echo "ERROR: File could not be updated. " . $e;
	} finally {
		refresh(); // reload to update select options
	}
}

require $_SERVER['DOCUMENT_ROOT']."/inc/header.php";

?>
<div class="container">
	<h5 class="text-center">Editing <?=SERVER_NAME?>'s server.properties</h5>
	<a class="btn btn-info" href="../?sid=<?=$sid?>">Back</a>
	<br /><br />
	<form method="POST">
		<div class="form-group">
			<label for="server-port">Port</label>
			<input id="server-port" name="server-port" value="<?=$server_port?>" class="form-control" type="text">
		</div>

		<div class="form-group">
			<label for="motd">MOTD</label>
			<input id="motd" name="motd" value="<?=$motd?>" class="form-control" type="text">
		</div>
		
		<div class="form-group">
			<label for="mp">Max Players</label>
			<input id="mp" name="max-players" value="<?=$max_players?>" class="form-control" type="text">
		</div>

		<div class="form-group">
			<label for="ln">Level Name</label>
			<input id="ln" name="level-name" value="<?=$level_name?>" class="form-control" type="text">
		</div>

		<div class="form-group">
			<label for="seed">Seed (empty for random)</label>
			<input id="seed" name="level-seed" value="<?=$level_seed?>" class="form-control" type="text">
		</div>
		
		<div class="form-group">
			<label for="om">Online Mode</label>
			<select id="om" name="online-mode" class="form-control">
				<?php
				foreach($boolean as $key => $om) {
					echo "<option ";
					if($om == $online_mode)echo "selected";
					echo " value='$om'>$om</option>";
				}
				?>
			</select>
		</div>
		
		<div class="form-group">
			<label for="sp">Spawn Protection</label>
			<input id="sp" name="spawn-protection" value="<?=$spawn_protection?>" class="form-control" type="text">
		</div>
		
		<div class="form-group">
			<label for="mws">Max World Size</label>
			<input id="mws" name="max-world-size" value="<?=$max_world_size?>" class="form-control" type="text">
		</div>
		
		<div class="form-group">
			<label for="gs">Generator Settings</label>
			<input id="gs" name="generator-settings" value="<?=$generator_settings?>" class="form-control" type="text">
		</div>

		<div class="form-group">
			<label for="gamemode">Gamemode</label>
			<select id="gamemode" name="gamemode" class="form-control">
				<?php
				foreach($gamemodes as $gm) {
					echo "<option ";
					if($gamemode == $gm)echo "selected";
					echo " value='$gm'>$gm</option>";
				}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="difficulty">Difficulty</label>
			<select id="difficulty" name="difficulty" class="form-control">
				<?php
				foreach($difficulties as $diff) {
					echo "<option ";
					if($diff == $difficulty)echo "selected";
					echo " value='$diff'>$diff</option>";
				}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="lt">Level Type</label>
			<select id="lt" name="level-type" class="form-control">
				<?php
				foreach($level_types as $key => $lt) {
					echo "<option ";
					if($lt == $level_type)echo "selected";
					echo " value='$lt'>$lt</option>";
				}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="ecb">Enable Command Blocks</label>
			<select id="ecb" name="enable-command-block" class="form-control">
				<?php
				foreach($boolean as $key => $cbo) {
					echo "<option ";
					if($cbo == $enable_command_block)echo "selected";
					echo " value='$cbo'>$cbo</option>";
				}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="view-distance">View Distance (<span id="vd-output"></span>)</label>
			<input id="view-distance" name="view-distance" value="<?=$view_distance?>" class="form-control" type="range" min="1" max="32">
		</div>

		<div class="col text-center">
			<input class="btn btn-primary text-center" type="submit" value="Edit Properties" name="submit">
		</div>
	</form>
</div>

<script>
	var slider = document.getElementById("view-distance");
	var output = document.getElementById("vd-output");
	output.innerHTML = slider.value;

	slider.oninput = function() {
		output.innerHTML = this.value;
	}
</script>