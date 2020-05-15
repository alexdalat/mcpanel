<?php require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php";require $_SERVER['DOCUMENT_ROOT'] . "/inc/func.php";require $_SERVER['DOCUMENT_ROOT'] . "/inc/auth.php";
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
?>
<div class="container">
	<div class="row">
		<div class="col-md center-block">
			<a class="btn btn-info" style="width:100%;" href="jars/">Server Jars</a>
		</div>
	</div>
	<br />
	<div class="col">
    	<table class="table table-striped border border-top-0 border-light">
            <tbody>
                <tr class="text-center">
                    <th>ID</th>
                    <th>Name</th>
					<th>Icon</th>
                    <th>Domain:Port</th>
                    <th>Status</th>
					<th>Version</th>
					<th>Minimum RAM</th>
					<th>Maximum RAM</th>
					<th>Owner</th>
                    <th>Preferences</th>
                    <th>Console</th>
                </tr>
                <?php
                require($_SERVER['DOCUMENT_ROOT']."/inc/db.php");
                $sql = "SELECT * FROM servers";
                $result = mysqli_query($con, $sql);
                while($row = mysqli_fetch_assoc($result)){
					$sql2 = "SELECT username FROM users WHERE id='$row[ownerid]'";
					$result2 = mysqli_query($con, $sql2);
					$row2 = mysqli_fetch_assoc($result2);
                    echo '<tr>';
					echo '<td>'.$row['id'].'</td>';
					echo '<td class="fit">'.$row['name'].'</td>';
					$servericon = glob($row['server_root_dir']."/server-icon.*");
					if(!empty($servericon)) {
						$img = basename($servericon[0]);
						$dir = $row['server_root_dir'];
						echo "<td><img src='/image.php?dir=$dir&img=$img' style='height:41px'></td>";
					} else {
						echo "<td>None</td>";
					}
					echo '<td class="fit">'.$row['domain'].':'.$row['port'].'</td>';
					echo '<td>'. (@fsockopen($row['ip'], $row['port'], $errno, $errstr, 6) ? "<p class='text-success my-auto'>Online</p>" : "<p class='text-danger my-auto'>Offline</p>") .'</td>';
					echo '<td>'.$row['server-jar'].'</td>';
					echo '<td>'.$row['mRam'].'MB</td>';
					echo '<td>'.$row['xRam'].'MB</td>';
					echo '<td>'.$row2['username'].'</td>';
					echo "<td><a href='pref/index.php?sid=".$row['id']."' class='btn btn-block btn-secondary text-white'>Preferences</a></td>";
					echo "<td><a href='console/index.php?sid=".$row['id']."' class='btn btn-block btn-dark text-success'>Console</a></td>";
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
	
	<hr>
	
	<div class="col-6">
		<table class="table table-striped border border-top-0 border-light">
			<tbody>
				<tr class="text-center">
					<th>SRV RECORDS</th>
				</tr>
				<?php
				require $_SERVER['DOCUMENT_ROOT'] . "/inc/lib/php/vendor/autoload.php";
				$key = new \Cloudflare\API\Auth\APIKey($cf['auth_email'], $cf['auth_key']);
				$adapter = new \Cloudflare\API\Adapter\Guzzle($key);

				$dns = new \Cloudflare\API\Endpoints\DNS($adapter);
				foreach ($dns->listRecords($cf['zone_id'])->result as $record) {
					if($record->type != "SRV")continue;
					#print_r($record);
					echo "<tr><td>";
					$content = preg_replace("/\s+/", " ", $record->content);
					$value = explode(" ",$content); # 0 = weight, 1 = port, 2 = target
					echo $record->data->name.".".$record->zone_name. " => " . $record->data->target.":".$record->data->port;
					echo "</td></tr>";
				}
				?>
			</tbody>
        </table>
    </div>
</div>