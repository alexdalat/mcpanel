<?php
if(!isset($_POST['clone_submit']) || !isset($_POST['server_root']))return header("Location: ./");
$world_types = ['world/', 'world_nether/', 'world_the_end/'];
$dir = $_POST['server_root'];
if(!is_dir($dir."world/")) {
	return "No world folder found!";
}
$id = "d".base_convert(time(), 10, 36);
$name = (isset($_POST['name'])?$_POST['name']:"A random world");
$name .= "_{$id}_.zip";
// Initialize archive object
$zip = new ZipArchive();
$zip->open($name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

foreach($world_types as $world) {
	if(!is_dir($dir.$world))continue;
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($dir.$world),
		RecursiveIteratorIterator::LEAVES_ONLY); // loop through each file in that folder

	$zip->addEmptyDir($world);
	foreach ($files as $named => $file) {
		// Skip directories (they would be added automatically)
		if (!$file->isDir()) {
			// Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($dir));

			// Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}
}

// Zip archive will be created only after closing object
$zip->close();

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=".$name);
header("Content-Length: " . filesize($name));
readfile($name);

unlink(getcwd()."/".$name);