<?php
$sid = $_GET['sid'];
if(!isset($sid))header("Location: ../");

require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require $_SERVER['DOCUMENT_ROOT']."/inc/db.php";
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");

require($_SERVER['DOCUMENT_ROOT'] . "/inc/func.php");
if(!ownServer($con, $sid)) {
	header("Location: ../");
}

$d = $_GET['d'];
$dir = SERVER_ROOT_DIR."/".$d."/";
if (strpos($dir, "../") || strpos($dir, "./") !== false) header("Location: manager.php?sid=$sid");

if(isset($_POST['addSubmit'])) {
	echo uploadFolder($dir, "fileUpload", "world");
	return refresh();
}

require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php";
?>
<div class="container">
	<a class="btn btn-info" href="../?sid=<?=$sid?>">Back</a>
	<br /><hr>
	<ul class="list-group list-group">
		<li class='list-group-item active'>
			<?php
			echo "<a class='no-style-button btn-link' href='manager.php?sid=$sid'>%ROOT%</a>";
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
			$world_names = ["world", "world_nether", "world_the_end"];
			if ($handle = opendir($dir)) {
				if(dir_is_empty($dir)) echo "<li class='list-group-item'>No files found.</li>";
				while (false !== ($entry = readdir($handle))) {
					if ($entry == "." || $entry == ".." || $entry == "latest") continue;
					if($dir == SERVER_ROOT_DIR."//") { # at root, only show worlds
						//if(!in_array($entry, $world_names))continue;
						if(!is_dir($dir.$entry))continue;
					}
					if(is_dir($dir.$entry)) {
						echo "<a href='manager.php?sid=$sid&d=$d/$entry' class='list-group-item list-group-item-action d-flex justify-content-between'>$entry/
						<button onClick=\"DeleteFile('".$d."/".$entry."')\" class='no-style-button close'><i class='fas fa-trash-alt'></i></button></a>";
					} else {
						echo "<a href='#' class='list-group-item list-group-item-action d-flex justify-content-between'>$entry
						<button onClick=\"DeleteFile('".$d."/".$entry."')\" class='no-style-button close'><i class='fas fa-trash-alt'></i></button></a>";
					}
				}
				closedir($handle);
			} else {
				echo "<li class='list-group-item'>Error: cannot open this directory.</li>";
			}
		?>
	</ul>
	<br />
	<form method="post" enctype="multipart/form-data">
		<div class="row">
			<div class="col-4">
				<div class="form-group">
					<!--Upload-->
					<div class="custom-file">
						<input class="custom-file-input" type="file" name="fileUpload[]" id="fileUpload" multiple="" directory="" webkitdirectory="" mozdirectory="" />
						<label class="custom-file-label" for="fileUpload">Upload something...</label>
					</div>
				</div>
			</div>
			<div class="col text-left">
				<input class="btn btn-primary text-center" type="submit" value="Upload" name="addSubmit">
			</div>
		</div>
	</form>
</div>
<script>
// Add the following code if you want the name of the file appear on select
$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
function DeleteFile(FileID) {
    var DelFile = new XMLHttpRequest();
    var url = 'delete.php';
    var params = 'File=' + FileID + '&sid=' + <?=$sid?>;
    DelFile.open('POST', url, true);
    DelFile.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    DelFile.onreadystatechange = function() {
		console.log("status: "+DelFile.status)
        if (DelFile.readyState == 4 && DelFile.status == 200) {
			location.reload()
        }
		console.log("response: "+DelFile.response);
    }
    DelFile.send(params);
    return true;
}
</script>