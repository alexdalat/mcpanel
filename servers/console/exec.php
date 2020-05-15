<?php

if(isset($_POST['cmd'])) {
	if(isset($_POST['sid'])) {
		$cmd = $_POST['cmd'];
		if ($cmd) {
				$sid = $_POST['sid'];
				require("getConfig.php");
				include("class/eventLogger.class.php");
			$elog = new eventLogger();

			$ss = shell_exec("netstat -tulpn | grep :".SERVER_PORT);
			if ($cmd == "start") {
				$elog->elog("Recieved start command.");
				if (!$ss) {
					file_put_contents(SERVER_ROOT_DIR.SERVER_LOG_DIR, "[" . date("h:i:s") . "] [SYSTM]: Recieved start command, starting server... \n", FILE_APPEND);
					$cmd = "cd ".SERVER_ROOT_DIR.";./start.sh";
					$output = shell_exec('sudo screen -S '.SCREEN_NAME.' -p 0 -X stuff "' .$cmd. '\n";');
					if($output) {
						echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, $output - Attempting to create the screen now...</div>";
						$elog->elog("  └─ $output");
						$output = shell_exec('sudo screen -dmS '.SCREEN_NAME.';');
						if($output2) {
							echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, $output2</div>";
							$elog->elog("  └─ $output2");
						} else {
							//echo "<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Success, created the screen with no problem.</div>";
							$elog->elog("  └─ Screen created.");
							$cmd = "cd " . SERVER_ROOT_DIR.";./start.sh";
							$output3 = shell_exec('sudo screen -S '.SCREEN_NAME.' -p 0 -X stuff "' .$cmd. '\n";');
							if($output3) {
								echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, $output2</div>";
								$elog->elog("  └─ $output2");
							} else {
								echo "<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Success, started the server with no problem.</div>";
								$elog->elog("  └─ Started the server successfully.");
							}
						}
					} else {
						echo "<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Success, started the server with no problem.</div>";
						$elog->elog("  └─ Started the server successfully.");
					}

				} else {
					echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, server is already running.</div>";
					$elog->elog("  └─ Error: Server is already running.");
				}
			} else if ($cmd == "stop") {
				  $elog->elog("Recieved stop command.");
				if ($ss) {
					file_put_contents(SERVER_ROOT_DIR.SERVER_LOG_DIR, "[" . date("h:i:s") . "] [SYSTM]: Recieved stop command, stopping server... \n", FILE_APPEND);
					$output = shell_exec('sudo screen -S '.SCREEN_NAME.' -p 0 -X stuff "' .$cmd. '\n";');
					if($output) {
						echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, $output</div>";
						$elog->elog("  └─ $output");
					} else {
						echo "<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Success, stopped the server with no problem.</div>";
						$elog->elog("  └─ Stopped the server successfully.");
					}
					$output = shell_exec('sudo screen -dr '.SCREEN_NAME.' -X quit;');

				} else {
					echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, server is not running.</div>";
					$elog->elog("  └─ Error: Server is not running..");
				}
			} else {
					  $elog->elog("Issued a server command: $cmd");
				if ($ss) {

					$output = shell_exec('sudo screen -S '.SCREEN_NAME.' -p 0 -X stuff "' .$cmd. '\n";');
					if($output) {
						echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, $output</div>";
						$elog->elog("  └─ $output");
					} else {
						echo "<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Success, Executed: " . $cmd . "</div>";
						$elog->elog("  └─ Successfully executed the command.");
					}

				} else {
					echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, server is not running. Execute 'start' to run it.</div>";
					$elog->elog("  └─ Error: Server is not running.");
				}
			}
		} else {
			echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, command cant be left blank.</div>";
		}

	} else {
		echo "<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, that server id is not found.</div>";
	}
	
}

?>