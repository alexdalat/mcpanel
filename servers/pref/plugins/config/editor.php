<?php
$sid = $_GET['sid'];
if(!isset($sid))header("Location: ../");
$f = $_GET['f'];
require($_SERVER['DOCUMENT_ROOT']."/inc/auth.php");
require $_SERVER['DOCUMENT_ROOT']."/inc/db.php";
require($_SERVER['DOCUMENT_ROOT']."/servers/console/getConfig.php");
require($_SERVER['DOCUMENT_ROOT']."/inc/func.php");
if(!ownServer($con, $sid)) {
	header("Location: ../");
}
require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php";

?><head>
	<link rel="stylesheet" href="/inc/lib/css/codemirror.css">
	<script src="/inc/lib/js/codemirror.js"></script>
	<script src="/inc/lib/js/yaml.js"></script>
</head>

<?php
$file = SERVER_ROOT_DIR."/plugins/".$f;

// check if form has been submitted
if (isset($_POST['text']) && isset($_POST['submitText']))
{
    file_put_contents($file, $_POST['text']);
} 
if(isset($_POST['resetText'])) {
	refresh();
}

// read the textfile
$text = file_get_contents($file);

?>
<!-- HTML form -->
<div class="container">
	<a class="btn btn-info" href="manager.php?sid=<?=$sid?>&d=<?php echo(dirname($f))?>">Back</a>
	<br /><hr>
	<form action="" method="post">
		<textarea id="textEditor" name="text"><?php echo htmlspecialchars($text) ?></textarea>
		<br />
		<input type="submit" value="Save" class="btn btn-success" name="submitText" />
		<input type="submit" value="Reset" class="btn btn-danger" name="resetText" />
	</form>
</div>

<script>
var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
textarea = document.getElementById("textEditor")
var editor = CodeMirror.fromTextArea(textarea, {
	lineNumbers: true,
	mode: "yaml",
	extraKeys: {
		Tab: (cm) => cm.execCommand("indentMore"),
		"Shift-Tab": (cm) => cm.execCommand("indentLess"),
	}
});
editor.setSize("100%", "75%")
</script>