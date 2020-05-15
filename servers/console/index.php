<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
?>
<?php require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");require($_SERVER['DOCUMENT_ROOT']."/inc/func.php");?>
<html style="height: 100%;">
    <head>
		<?php 
			$sid = $_GET['sid'];
			require("getConfig.php");
			if(!ownServer($con, $sid))header("Location: ../");
		?>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo SERVER_NAME . " Console"; ?></title>
        <link rel="stylesheet" media="screen, projection" href="css/core.css" />
        <link rel="stylesheet" media="screen, projection" href="css/font-awesome.css" />
        <link rel="stylesheet" media="screen, projection" href="css/notification.css" />
        <script src="js/jQuery.min.js"></script>
        <script language="javascript" src="js/jquery.timers-1.0.0.js"></script>
        <script language="javascript" src="js/notification.js"></script>
		
        <script type="text/javascript">
			$(document).ready(function() {
				var sid = <?=$sid?>;
				var status = "";
				var lld = "";
				var logstatus = "";
				var togglecmdi = 1;

				notify("<i class='fa fa-info-circle'></i> <div class='notification-content'> <div class='notification-header'>Tip</div> Clicking on the server status box (top right corner) refreshes the status.</div>");
				notify("<i class='fa fa-info-circle'></i> <div class='notification-content'> <div class='notification-header'>Tip</div> The command box pops up right after you start typing the command.</div>");

				gh();

				function gh() {
					checklogs();
					lld = "";
					status = "gh";
					$.post("reqman.php", {status: status, lld: lld, sid: sid}, function(clgh) {
						$("#console").html(clgh);
						notify("<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Successfully grabbed the log data.</div>");
						sd();
						status = "lld";
						$.post("reqman.php", {status: status, lld: lld, sid: sid}, function(cllld) {
							lld = cllld;
						});
						status = "ilu";
						$.post("reqman.php", {status: status, lld: lld, sid: sid}, function(clilu) {
							$("#infologs").html(clilu);
						});
					});
					setInterval(update, 500);
        	  	}
				  function checklogs() {
					logstatus = "check";
					$.post("lpc.php", {logstatus: logstatus, sid: sid}, function(lsd) {
						  if (lsd !== "-rw-rw-rw-+") {
								notify("<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, No log data was received. Requesting permission change of 'latest.log' to '666'.</div>");
								logstatus = "update";
								$.post("lpc.php", {logstatus: logstatus, sid: sid}, function(lpcd) {
									if (lpcd == "-rw-rw-rw-+") {
										  notify("<i class='fa fa-check-circle notification-success'></i> <div class='notification-content'> <div class='notification-header notification-success'>Success</div> Successfully changed permission of 'latest.log' to '666'. Refreshing logs...</div>");
											gh();
									  } else {
											notify("<i class='fa fa-times-circle notification-error'></i> <div class='notification-content'> <div class='notification-header notification-error'>Error</div> Error, Failed to change permission of 'latest.log' to '666'.</div>");
									  }
							  });
						}
					});
				  }
				  function update() {
					  status = "lld";
					  $.post("reqman.php", {status: status, lld: lld, sid: sid}, function(lldrdata) {
						  	//console.log("lldrdata: "+lldrdata);
							if (lldrdata == "error.logPermissionInvalid") {
									checklogs();
									return false;
							}
						  	//console.log(lldrdata+"\n"+lld)
							if (lldrdata !== lld) { // new line detected
								status = "clu";
								$.post("reqman.php", {status: status, lld: lld, sid: sid}, function(clurdata) {
									//console.log("clurdata: "+clurdata)
									$("#console").append(clurdata);
									sd();
									status = "ilu";
									$.post("reqman.php", {status: status, lld: lld, sid: sid}, function(clilu) {
										$("#infologs").html(clilu);
									});
								});
								lld = lldrdata;
							}
					  });
				  }
				function sd() {
					$('html, body').animate({ scrollTop: $('#sd').offset().top }, 'slow');
					//elmnt = document.getElementById("sd");
					//scrollTo(document.body, elmnt.offsetTop, 1250);  
				}

				$(document).on('keydown', function(e) {
					if(e.ctrlKey)return;
					var key = e.keyCode || e.charCode;
					if (key == 13) {
						// ENTER
						var cmd = $("#cmd-input").val();
						//cmd = cmd.replace(/([A-Za-z0-9]+)/, v => v.toLocaleLowerCase());
						$.post("exec.php", {cmd: cmd, sid: sid}, function(cmdrd) {
                			  $(".cmd-input").addClass("hidden");
                			  $("#cmd-input").val("");
                			  notify(cmdrd);
                		});
					} else if (key == 27) {
						// ESC
						$(".cmd-input").addClass("hidden");
						$("#cmd-input").val("");
					} else if (key == 8) {
						// BACKSPACE
						if (document.getElementById('cmd-input').value.length == 0) {
							$(".cmd-input").addClass("hidden");
							$("#cmd-input").val("");
						}
					} else {
						$(".cmd-input").removeClass("hidden");
						$("#cmd-input").focus();
					}
				});
				
				if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
					$("#console").click(function() {
						$(".cmd-input").removeClass("hidden");
						$("#cmd-input").focus();
					});
				}

				$(".server-status").click(function() {
					ss();
				});
				ss();

				function ss() {
					$.post("ss.php", {
						sid: sid
					}, function(ssd) {
						$(".server-status").html("Server is " + ssd).addClass("server-status-" + ssd);
						if (ssd == "online") {
							$(".server-status").removeClass("server-status-offline");
						} else {
							$(".server-status").removeClass("server-status-online");
						}
					});
				}

				function notify(content) {
					$.createNotification({
						content: content,
						duration: 5000
					});
				}
			});
        </script>
    </head>
    <body>

    <div class="update" id="console"></div>
    <div id="sd"></div>

    <div id="infologs"></div>
    <div class="cmd-input hidden">
        <div class="cmd-wrapper">
            <div class="cmd-type">/</div>
            <input type="text" id="cmd-input" placeholder="Enter a command... Press enter to execute." />
    	</div>
    </div>

    <div class="server-status" style="cursor: pointer; user-select: none;"></div>

    <div class="notification-board right top"></div>

    </body>
</html>
