<?php require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php";?>

<?php
if(isset($_POST['submit5475'])) {
    require($_SERVER['DOCUMENT_ROOT']."/inc/db.php");
    $username = stripslashes($_POST['username']);
    $username = mysqli_real_escape_string($con, $username);

    require($_SERVER['DOCUMENT_ROOT']."/inc/PasswordHash.php");
    $passwordHasher = new PasswordHash(8, TRUE);

    $sql = "SELECT id, username, rank, hash FROM `users` WHERE (email='$username' OR username='$username')";
    $result = mysqli_query($con, $sql)or die(mysqli_error($con));
    $rows = mysqli_num_rows($result);
    if($rows === 1) {
        $row = mysqli_fetch_assoc($result);
        $password = stripslashes($_POST['password']);
        $password = mysqli_real_escape_string($con, $password);
        $check = $passwordHasher->CheckPassword($password, $row['hash']);
        if($check === true) {
			$_SESSION['id'] = $row['id'];
			$_SESSION['rank'] = $row['rank'];
            $_SESSION['username'] = $row['username'];
			header("Location: /admin/");
        } else {
            echo "Incorrect username or password!";
        }
    } else {
        echo "Username not registered";
    }
}

?>

<br /><span id="errorBox"></span><br /><br />

<div class="col-2 offset-sm-5 text-center">
    <form name="login" onsubmit="return validateForm(this)" method="POST">
        <div class="form-group">
            <label for="userInput">Username:</label>
            <input id="userInput" class="form-control" type="text" maxlength="50" name="username" placeholder="Username or Email" />
        </div>
        <div class="form-group">
            <label for="passInput">Password:</label>
            <input id="passInput" class="form-control" type="password" maxlength="100" name="password" placeholder="Password" />
        </div>
        <input class="btn btn-primary" type="submit" value="Login" name="submit5475" />
    </form>
</div>

<script>
    function validateForm() {
        success = true;
        var err = document.getElementById("errorBox");
        err.innerHTML = '';
        var username = document.forms["login"]["username"].value;
        var password = document.forms["login"]["password"].value;
        var submit = document.forms["login"]["submit5475"].value;
        if(username.length < 3 || username.length > 25) {
            err.innerHTML += "Invalid username or email length! (3-25)<br />";
            success = false;
        }
        if(password.length < 6) {
            err.innerHTML += "Password must be at least 6 characters long!<br />";
            success = false;
        }
        if(password.length > 100) {
            err.innerHTML += "Password must be less than 100 characters!<br />";
            success = false;
        }
        if(success !== true) {
            return false;
        }
    }
</script>