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

// get all files with .jar in /plugin/ directory
$plugins = array();
foreach(glob(SERVER_ROOT_DIR."/plugins/*.jar") as $file) {
	$file = basename($file, ".jar");
	$plugins[] = $file;
}

if(isset($_POST['addSubmit'])) {
    // folder to save downloaded files to. must end with slash
    $destination_folder = SERVER_ROOT_DIR."/plugins/";
	if($_POST['pluginUrl'] != "")
    	$url = $_POST['pluginUrl'];
	else {
		echo uploadFile($destination_folder, "pluginUpload", "jar");
		return refresh();
	}
	$headers = get_headers($url);
	$filename = basename($headers[7]);
    $newfname = $destination_folder . $filename;
    $save = file_put_contents( $newfname, fopen($url, 'r'));
	//Check to see if it failed to save or not.
	if($save === false){
		console("Failed to upload file.");
	}
	return refresh();
}else if(isset($_POST['removeSubmit'])) {
	$file = SERVER_ROOT_DIR."/plugins/".$_POST['pluginDel'].".jar";
	unlink($file) or die("Couldn't delete file");
	return refresh();
} else if(isset($_POST['schemGet'])) {
	downloadFile(SERVER_ROOT_DIR."/plugins/WorldEdit/schematics/",$_POST['schemFile']);
} else if(isset($_POST['schemPush'])) {
	if (!file_exists(SERVER_ROOT_DIR."/plugins/WorldEdit/schematics/")) {
		mkdir(SERVER_ROOT_DIR."/plugins/WorldEdit/schematics/", 0777, true);
	}
	echo uploadFile(SERVER_ROOT_DIR."/plugins/WorldEdit/schematics/", "schemUpload", "schem, schematic");
	return refresh();
}
require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php";
?>
<title>Plugins for <?=SERVER_NAME?></title>
<p>&nbsp;</p>
<div class="container">
  <h5 class="text-center">Editing <?=SERVER_NAME?>'s Plugins</h5>
  <a class="btn btn-info" href="../?sid=<?=$sid?>">Back</a>
  <br /><br />
	
  <form method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label for="addPlugin">Add Plugin</label>
			<input id="addPlugin" name="pluginUrl" placeholder="Direct URL to plugin..." class="form-control" type="url">
		</div>
		
		
		<div class="form-group">
			Upload Plugin
			<div class="custom-file">
				<input class="custom-file-input" type="file" name="pluginUpload" id="pluginUpload" />
				<label class="custom-file-label" for="pluginUpload">Choose a jar plugin file...</label>
			</div>
		</div>
		
		<div class="col text-center">
			<input class="btn btn-primary text-center" type="submit" value="Add" name="addSubmit">
		</div>
	</form>
	
	<form method="post">
		<div class="form-group">
			<label for="pluginList">Plugin List / Remove Plugin</label>
			<select id="pluginList" name="pluginDel" class="form-control">
				<?php
				foreach($plugins as $key => $pluginOpt) {
					echo "<option value='$pluginOpt'>$pluginOpt</option>";
				}
				?>
			</select>
		</div>
		
		<div class="col text-center">
			<input class="btn btn-primary text-center" type="submit" value="Remove" name="removeSubmit">
		</div>
	</form>
	
	<br />
	<h3 class="text-center">Customs</h3>
	<hr>
	<a class="btn btn-info" style="width:100%;" href="config/manager.php?sid=<?=$sid?>">Plugin Configurations</a>
	<br /><br />
	
	<div class="row">
		<div class="col">
			<form method="post">
				<div class="card">
					<div class="card-header">
					Schematics
					</div>
					<ul class="list-group list-group-flush">
						<?php
						$schems = [];
						foreach(glob(SERVER_ROOT_DIR."/plugins/WorldEdit/schematics/*.{schem,schematic}", GLOB_BRACE) as $file) {
							$file = basename($file);
							echo "<li class='list-group-item'>
								<input class='no-style-button btn-link' type='submit' name='schemGet' value='$file' />
								<button onClick=\"DeleteFile('".$file."')\" class='no-style-button close'>&times;</button>
								</li>";
						}
						?>
					</ul>
				</div>
			</form>
		</div>
		
		<div class="col">
			<form method="post" enctype="multipart/form-data">
				<div class="card">
					<div class="card-header">
						Upload schematics
					</div>
					<div class="card-body">
						<div class="custom-file">
							<input class="custom-file-input" type="file" name="schemUpload" id="schemUpload" />
							<label class="custom-file-label" for="schemUpload">Choose a schematic file...</label>
						</div>
					</div>
					<div class="card-footer">
						<input class="btn btn-primary" type="submit" value="Upload Schematic" name="schemPush" />
					</div>
				</div>
			</form>
		</div>
	</div>
	
</div>

<script>
// Add the following code if you want the name of the file appear on select
$(".custom-folder-input").on("change", function() {
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
        if (DelFile.readyState == 4 && DelFile.status == 200) {
            console.log(DelFile.response);
			location.reload()
        }
    }
    DelFile.send(params);
    return true;
}
</script>