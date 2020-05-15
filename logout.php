<?php
require $_SERVER['DOCUMENT_ROOT'] . "/inc/auth.php";
session_destroy();
header("Location: /")
?>