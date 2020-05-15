<?php 
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$sid = -1;

require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require $_SERVER['DOCUMENT_ROOT']."/inc/header.php";
require $_SERVER['DOCUMENT_ROOT']."/inc/db.php";
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");
require($_SERVER['DOCUMENT_ROOT']."/inc/func.php");

// get all files with .jar in /build/ directory
$jars = array();
foreach(glob(BUILD_ROOT_DIR."/*.jar") as $file) {
	$file = basename($file, ".jar");
	if($file == "BuildTools")continue;
	$jars[] = $file;
}

if(isset($_POST['addSubmit'])) {
    // folder to save downloaded files to. must end with slash
    $destination_folder = BUILD_ROOT_DIR;
    $url = $_POST['jarUrl'];
	if(basename($url, ".jar") == "") {
		$headers = get_headers($url);
		$filename = basename($headers[7]);
	} else $filename = basename($url, ".jar");
	if($_POST['jarName'] != "") $filename = $_POST['jarName'];
	if($filename == "server"){
		echo "Change the filename please!";
		return refresh();
	}
	console($filename);
    $newfname = $destination_folder . $filename . ".jar";
	console($newfname);
    $save = file_put_contents( $newfname, fopen($url, 'r'));
	//Check to see if it failed to save or not.
	if($save === false){
		console("Failed to upload file.");
	}
	return refresh();
}

if(isset($_POST['removeSubmit'])) {
	$file = BUILD_ROOT_DIR."/".$_POST['jarDel'].".jar";
	unlink($file) or die("Couldn't delete file");
	return refresh();
}

?>
<title>Server Jars</title>
<div class="container">
	<h5 class="text-center">Accessing the Server Jars</h5>
	
	<form method="post">
		<div class="form-group">
			<label for="addJar">Add A Server Jar</label>
			<input id="addJar" name="jarUrl" placeholder="Direct URL to the server jar..." class="form-control" type="url">
		</div>
		
		<div class="form-group">
			<label for="jarName">Change Server Jar Name</label>
			<input id="jarName" name="jarName" placeholder="Filename for the new server jar..." class="form-control" type="text">
			<small id="jarName" class="form-text text-muted">Leave this value empty to get the filename from the URL. You will be prompted to change the filename if the file is named server.jar (minecraft.net server files). <i>Keep the name formats of the previous files</i></small>
		</div>
		
		<div class="col text-center">
			<input class="btn btn-primary text-center" type="submit" value="Add" name="addSubmit">
		</div>
	</form>
	
	<form method="post">
		<div class="form-group">
			<label for="jarList">Version List / Remove Version</label>
			<select id="jarList" name="jarDel" class="form-control">
				<?php
				foreach($jars as $key => $jarOpt) {
					echo "<option value='$jarOpt'>$jarOpt</option>";
				}
				?>
			</select>
		</div>
		
		<div class="col text-center">
			<input class="btn btn-primary text-center" type="submit" value="Remove" name="removeSubmit">
		</div>
	</form>
	
</div>