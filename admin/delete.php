<?php require $_SERVER['DOCUMENT_ROOT'] . "/inc/auth.php";require $_SERVER['DOCUMENT_ROOT'] . "/inc/func.php";?>
<?php
if(!isset($_POST['id'])){
    header("Location: /admin/");
} else {
    if($_POST['id'] !== $_SESSION['id'] && !isAdmin($_SESSION['rank']))
		return header("Location: ./");
    require($_SERVER['DOCUMENT_ROOT']."/inc/db.php");
    $sql = "DELETE FROM users WHERE id='$_POST[id]'";
    $result = mysqli_query($con, $sql);
    if($result){
        return true;
    } else {
        return false;
    }
}
?>