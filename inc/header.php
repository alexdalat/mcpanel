<?php
if ( session_status() == PHP_SESSION_NONE ) {
	session_start(); //Important
}
?><head>
	<link href="/inc/lib/css/fontawesome.css" rel="stylesheet" type="text/css">
	<link href="/inc/lib/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css">
	<style>
		@media screen and (max-device-width: 480px) {
			body {
				-webkit-text-size-adjust: none;
			}
			.iframe-div {
				overflow-y: scroll;
			}
		}
		
		tr,
		tr> th,
		tr> td {
			text-align: center;
			vertical-align: middle !important;
		}
		
		.no-style-button {
			background: none;
			color: inherit;
			border: none;
			padding: 0;
			font: inherit;
			cursor: pointer;
			outline: inherit;
			text-decoration: none !important;
		}
		
		.table-custom {
			background-color: #f7f7f7;
		}
		
		.table-custom td.fit,
		.table-custom th.fit {
			white-space: nowrap;
			width: 1%;
		}
	</style>
</head>
<script src="/inc/lib/js/jquery-3.3.1.min.js"></script>
<script src="/inc/lib/js/bootstrap.min.js"></script>
<script src="/inc/lib/js/bootstrap-table.min.js"></script>
<script src="/inc/app/js/main.js"></script>

<?php
if ( isset( $_SESSION[ 'username' ] ) ) {
	// update recent timestamp
	require( $_SERVER[ 'DOCUMENT_ROOT' ] . "/inc/db.php" );
	$time = time();
	$sql = "UPDATE users SET lastSeen='$time' WHERE username='$_SESSION[username]'";
	mysqli_query( $con, $sql );
	?>

	
<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-primary">
	<div class="container">
		<a class="navbar-brand" href="#">Founders</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>


		<div class="collapse navbar-collapse" id="navbarToggler">
			<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
				<li class="nav-item">
					<a class="nav-link" href="/admin/">Users</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/servers/">Servers</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Info
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
						<a class="dropdown-item" href="/info/todo/">TODO</a>
					</div>
				</li>
			</ul>
			<form class="form-inline my-2 my-lg-0">
				<a class="btn btn-secondary my-2 my-sm-0" href="/logout.php">Logout</a>
			</form>
		</div>
	</div>
</nav>
<br/>
<?php } ?>
