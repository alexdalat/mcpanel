<?php
$sid = $_GET['sid'];
if(!isset($sid))header("Location: ../");
$d = $_GET['d'];

require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require $_SERVER['DOCUMENT_ROOT']."/inc/db.php";
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");

require($_SERVER['DOCUMENT_ROOT'] . "/inc/func.php");
if(!ownServer($con, $sid)) {
	header("Location: ../");
}

require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php";
?>
<div class="container">
	<a class="btn btn-info" href="../?sid=<?=$sid?>">Back</a>
	<br /><br />
	<ul class="list-group list-group">
		<li class='list-group-item active'>
			<?php
			echo "<a class='no-style-button btn-link' href='manager.php?sid=$sid'>plugins</a>";
			$dirs = explode("/", $d);
			foreach ($dirs as $i => $chunk) {
				echo sprintf(
					'<a class="no-style-button btn-link" href="manager.php?sid=%s&d=%s">%s</a>/',
					$sid,
					implode('/', array_slice($dirs, 0, $i + 1)),
					$chunk
				);
			}
	   		?>
		</li>
		<?php
			$dir = SERVER_ROOT_DIR."/plugins/".$d."/";
		    if (strpos($dir, "../") || strpos($dir, "./") !== false) header("Location: manager.php?sid=$sid");
			if ($handle = opendir($dir)) {
				if(dir_is_empty($dir)) echo "<li class='list-group-item'>No files found.</li>";
				while (false !== ($entry = readdir($handle))) {
					if ($entry == "." || $entry == ".." || $entry == "latest") continue;
					if(!is_dir($dir.$entry)) {
						echo "<a href='editor.php?sid=$sid&f=$d/$entry' class='list-group-item list-group-item-action'>$entry</a>";
					} else {
						echo "<a href='manager.php?sid=$sid&d=$d/$entry' class='list-group-item list-group-item-action'>$entry/</a>";
					}
				}
				closedir($handle);
			} else {
				echo "<li class='list-group-item'>Error: cannot open this directory.</li>";
			}
		?>
	</ul>
</div>