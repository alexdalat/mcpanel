<?php
require $_SERVER['DOCUMENT_ROOT']."/inc/config.php";
function refresh($t=0) {
	return header("Refresh:$t");
}
function console($t) {
	echo "<script>console.log('$t')</script>";
}
function alert($t) {
	echo "<script>alert('$t')</script>";
}
function keygen($length=10)
{
	$key = '';
	list($usec, $sec) = explode(' ', microtime());
	mt_srand((float) $sec + ((float) $usec * 100000));
	
   	$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

   	for($i=0; $i<$length; $i++)
	{
   	    $key .= $inputs{mt_rand(0,61)};
	}
	return $key;
}
function ownServer($con, $sid) {
	$sql = "SELECT ownerid, hasaccess FROM servers WHERE id='$sid'";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$usr_arr = explode (",", $row['hasaccess']);  
	if(in_array($_SESSION['id'], $usr_arr) || $_SESSION['id'] == $row['ownerid'] || isAdmin($_SESSION['rank']))return true; // if is owner or has access or is user with id 1
	return false;
}
function ranks_under($rank, $include=false) {
	global $available_ranks; // use from config
	$temp_array = $available_ranks;
	return array_splice($temp_array, array_search($rank, $temp_array)+($include?0:1));
}
function isAdmin($rank) {
	global $available_ranks; // use from config
	if(!in_array($rank, $available_ranks))
		return false;
	$temp_array = ranks_under("Admin"); // all players under Admin
	if(!in_array($rank, $temp_array))
		return true;
	return false;
}
function isHigher($rank, $rank2) {
	global $available_ranks; // use from config
	$level1 = array_search($rank, $available_ranks);
	$level2 = array_search($rank2, $available_ranks);
	if($level1 <= $rank2) {
		return true;
	} else {
		return false;
	}
}
function del_file($file) {
	if(unlink($file))
		return "Delete successful.";
	else 
		return "Delete failed.";
}
function del_dir($dir) {
	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it,
				 RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file) {
		if ($file->isDir()){
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}
	rmdir($dir.$world);
}
function del_worlds($dir) {
	$world_types = ['world/', 'world_nether/', 'world_the_end/'];
	if(!is_dir($dir."world/")) {
		return "No world folder found!";
	}
	
	foreach ($world_types as $world) {
		if(!is_dir($dir.$world))continue;
		$it = new RecursiveDirectoryIterator($dir.$world, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it,
					 RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir.$world);
	}
}
function downloadFile($dir, $filename) {
	$file = $dir.$filename;
	if(file_exists($file)){
		//Get file type and set it as Content Type
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		header('Content-Type: ' . finfo_file($finfo, $filename));
		finfo_close($finfo);
		//Use Content-Disposition: attachment to specify the filename
		header('Content-Disposition: attachment; filename='.basename($filename));
		//No cache
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		//Define file size
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}
function uploadFile($target_dir, $file_id = "fileToUpload", $type = null, $name = false) {
	$cloudfile = $_FILES[$file_id]['tmp_name'];
	$target_file = $target_dir . basename($_FILES[$file_id]["name"]);
	$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	if($name)
		$new_destination_file = $target_dir . $name.".".$fileType;
	else
		$new_destination_file = $target_file;
	$types = explode(", ", $type);
	// Check if file already exists (not needed since it overrides)
	/*if ($result = glob(SERVER_ROOT_DIR."/favicon.*")) {
		if(count($result) > 1)
			return "Too many favicons, please contact the website owner.";
		echo "File already exists. Replacing...";
		del_file($result[0]);
	}
	// Check file size (both ways)
	list($width, $height) = getimagesize($cloudfile);
	if($width > 64 || $height > 64) {
		return "Sorry, your file is too big. Under 64x64 pixels please.";
	}*/
	if ($_FILES[$file_id]["size"] > 250000000) { // 1 mb = 1,000,000 bytes
		return "Sorry, your file is too large. Under 250 mb please.";
	}
	// Allow certain file formats
	$available_types = join(", ", $types);
	if(isset($type) && !in_array($fileType, $types)) {
		return "Sorry, only $available_types files are allowed.";
	} else {
		if (move_uploaded_file($_FILES[$file_id]["tmp_name"], $new_destination_file)) {
			return "The file ". basename( $_FILES[$file_id]["name"]). " has been uploaded.";
		} else {
			return "Sorry, there was an error uploading your file.";
		}
	}
}
function uploadFolder($target_dir, $file_id = "folderToUpload", $foldername = "uploaded_folder") {
	if($foldername != "") {
  		$foldername=$target_dir.$foldername;
  		if(!is_dir($foldername))
  			mkdir($foldername);
  		foreach($_FILES['files']['name'] as $i => $name) {
  			if(!is_dir($foldername."/".$name)) {
  				mkdir($foldername."/".$name);
  				if(strlen($_FILES['files']['name'][$i]) > 1) {
  					move_uploaded_file($_FILES['files']['tmp_name'][$i],$foldername."/".$name);
  				}
  			}
  			else {
  				if(strlen($_FILES['files']['name'][$i]) > 1) {
					move_uploaded_file($_FILES['files']['tmp_name'][$i],$foldername."/".$name);
  				}
  			}
  		}
  		return "Folder is successfully uploaded to ".$foldername;
  	} else
  		return "Upload folder name is empty";
	
}
function listFolderFiles($dir){
    $ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    // prevent empty ordered elements
    if (count($ffs) < 1)
        return;

    echo '<ol>';
    foreach($ffs as $ff){
        echo '<li>'.$ff;
        if(is_dir($dir.'/'.$ff)) listFolderFiles($dir.'/'.$ff);
        echo '</li>';
    }
    echo '</ol>';
}
function listFiles($dir) {
	if ($handle = opendir($dir)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				echo "$entry\n";
			}
		}
    	closedir($handle);
	}
}
function dir_is_empty($dir) {
  $handle = opendir($dir);
  while (false !== ($entry = readdir($handle))) {
    if ($entry != "." && $entry != "..") {
      closedir($handle);
      return FALSE;
    }
  }
  closedir($handle);
  return TRUE;
}