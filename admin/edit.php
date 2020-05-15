<?php require $_SERVER['DOCUMENT_ROOT']."/inc/auth.php"; require $_SERVER['DOCUMENT_ROOT']."/inc/func.php";?>
<?php
require($_SERVER['DOCUMENT_ROOT']."/inc/db.php");
$id = $_POST['id'];
if($id !== $_SESSION['id'] && !isAdmin($_SESSION['rank']))
	return header("Location: ./");
$username = mysqli_real_escape_string($con, stripslashes($_POST['username']));
$rank = mysqli_real_escape_string($con, stripslashes($_POST['rank']));
$password = mysqli_real_escape_string($con, stripslashes($_POST['password']));
$password2 = mysqli_real_escape_string($con, stripslashes($_POST['password2']));
$email = mysqli_real_escape_string($con, stripslashes($_POST['email']));

$sql = "SELECT * FROM `users` WHERE (username='$username' OR email='$email') AND NOT id='$id'";
$result = mysqli_query($con, $sql);
$rows = mysqli_num_rows($result);
if($rows >= 1) { // if user or email already used
	$sql = "SELECT * FROM `users` WHERE email='$email' AND NOT id='$id'";
	$result = mysqli_query($con, $sql);
	$rows = mysqli_num_rows($result);
	if($rows >= 1) { // email in use
		$return_arr[] = array("error" => "email",
							  "value" => $email);
		echo json_encode($return_arr);
	} else { // username in use
		$return_arr[] = array("error" => "username",
							  "value" => $username);
		echo json_encode($return_arr);
	}
} else {
	if(preg_match("^[0-9a-zA-Z_.-]+$", $username) || strlen($username) < 3 || strlen($username) > 25) {
		$return_arr[] = array("error" => "username",
							  "value" => $username);
		echo json_encode($return_arr);
		return;
	}
	if($password == "") {
		$sql = "UPDATE users SET id = '$id', username = '$username', rank = '$rank', email = '$email' WHERE id='$id'";
		$result = mysqli_query($con, $sql);
	} else if($password != "") {
		if(strlen($password) < 6 || strlen($password) > 100) {
			$return_arr[] = array("error" => "password",
								  "value" => "syntax");
			echo json_encode($return_arr);
			return;
		}
		if($password !== $password2) {
			$return_arr[] = array("error" => "password",
								  "value" => "match");
			echo json_encode($return_arr);
			return;
		}
		require($_SERVER['DOCUMENT_ROOT']."/inc/PasswordHash.php");
		$passwordHasher = new PasswordHash(8, TRUE);
		$hashedPassword = $passwordHasher->HashPassword($password);
		$sql = "UPDATE users SET id = '$id', username = '$username', ";
		if(isAdmin($_SESSION['rank']))$sql .= "rank = '$rank', ";
		$sql .= "hash = '$hashedPassword', email = '$email' WHERE id='$id'";
		$result = mysqli_query($con, $sql);
	}
	$return_arr[] = array("success" => true);
	if($id == $_SESSION['id']) {
		if($_SESSION['rank'] != $rank) {
			$return_arr[] = array("success" => true, "reload" => "true");
		}
		$_SESSION['rank'] = $rank;
		$_SESSION['username'] = $username;
	}
	echo json_encode($return_arr);
}
?>