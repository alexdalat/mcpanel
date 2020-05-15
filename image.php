<?php
  header('Content-Type: image/png');
  readfile($_GET['dir'] . $_GET['img']);
?>