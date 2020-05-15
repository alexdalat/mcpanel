<?php 
require $_SERVER['DOCUMENT_ROOT'] . "/inc/auth.php";
require($_SERVER['DOCUMENT_ROOT']."/inc/db.php");
require $_SERVER['DOCUMENT_ROOT'] . "/inc/func.php";
if($id !== $_SESSION['id'] && !isAdmin($_SESSION['rank']))
	return header("Location: ./");
// removes backslashes
$username = stripslashes($_POST['username']);
//escapes special characters in a string
$username = mysqli_real_escape_string($con, $username);
$rank = stripslashes($_POST['rank']);
$rank = mysqli_real_escape_string($con, $rank);
$email = stripslashes($_POST['email']);
$email = mysqli_real_escape_string($con, $email);

$sql = "SELECT * FROM `users` WHERE username='$username' OR email='$email'";
$result = mysqli_query($con, $sql);
$rows = mysqli_num_rows($result);
if($rows >= 1) { // if user or email already used
	$sql = "SELECT * FROM `users` WHERE email='$email'";
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
} else { // available username & email
	if(preg_match("^[0-9a-zA-Z_.-]+$", $username) || strlen($username) < 3 || strlen($username) > 25) {
		$return_arr[] = array("error" => "username",
							  "value" => $username);
		echo json_encode($return_arr);
		return;
	}
	$password = stripslashes($_POST['password']);
	$password = mysqli_real_escape_string($con, $password);
	$password2 = stripslashes($_POST['password2']);
	$password2 = mysqli_real_escape_string($con, $password2);
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

	$time = time();
	$sql = "INSERT INTO `users` (username, email, hash, lastSeen, created) VALUES ('$username', '$email', '$hashedPassword', '$time', '$time')";
	if(mysqli_query($con, $sql)){
		$id = mysqli_insert_id($con);
		$time = date('m-d-Y H:i:s', $time);
		$return_arr[] = array("id" => $id,
                      "username" => $username,
                      "email" => $email,
					  "rank" => $rank,
                      "lastSeen" => $time);
		echo json_encode($return_arr);
	} else{
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
	}
}
?>