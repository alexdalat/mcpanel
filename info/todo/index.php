<?php require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php"; require $_SERVER['DOCUMENT_ROOT'] . "/inc/auth.php"; require $_SERVER['DOCUMENT_ROOT'] . "/inc/func.php";?>

<div class="container">
	<div class="text-center">
		<h1 class="display-4"><b>Upcoming features</b></h1>
	</div>
	<br />
	<div class="list-group">
		<?php
		require( $_SERVER[ 'DOCUMENT_ROOT' ] . "/inc/db.php" );
		
		$sql = "SELECT * FROM upcoming ORDER BY date DESC ";
		$res = mysqli_query( $con, $sql );
		foreach($res as $r) { ?>
			<div class="list-group-item list-group-item-action">
				<div class="d-flex w-100 justify-content-between">
					<h4><?=$r["title"]?></h4>
					<small><?php
						echo date_format(date_create($r["date"]), 'F d, Y h:i a');?></small>
				</div>        
				<p><?=$r["content"]?></p>
			</div>
		<?php } ?>
	</div>
</div>