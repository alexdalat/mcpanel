<?php require $_SERVER['DOCUMENT_ROOT'] . "/inc/header.php"; require $_SERVER['DOCUMENT_ROOT'] . "/inc/auth.php"; require $_SERVER['DOCUMENT_ROOT'] . "/inc/func.php";?>

<?php
error_reporting( E_ALL );
ini_set( 'display_errors', TRUE );
ini_set( 'display_startup_errors', TRUE );
?>

<div class="container">
	<div class="text-center">
		<br/>
		<h1 class="display-4"><em>Welcome back <strong><?=$_SESSION['username']?></strong>!</em></h1>
	</div>
	<div id="successBox"></div>
	<div class="col">
		<table class="table table-hover table-custom table-striped" data-toggle="table" data-pagination="true" data-search="true" data-sort-name="lastSeen">
			<thead class="thead-dark">
				<tr>
					<th data-field="id" scope="col" data-sortable="true">ID</th>
					<th data-field="username" scope="col" data-sortable="true">Username</th>
					<th data-field="email" scope="col">Email</th>
					<th data-field="rank" scope="col" data-sortable="true" data-sorter="rankSorter">Rank</th>
					<th data-field="lastSeen" scope="col" data-sortable="true" data-sorter="datesSorter">Last Seen</th>
					<th class="fit" scope="col">Edit</th>
					<th class="fit" scope="col">Delete</th>
				</tr>
			</thead>
			<tbody>
				<?php
				require( $_SERVER[ 'DOCUMENT_ROOT' ] . "/inc/db.php" );
				$sql = "SELECT id, username, email, rank, lastSeen FROM users";
				$result = mysqli_query( $con, $sql );
				while ( $row = mysqli_fetch_assoc( $result ) ) {
					$epoch = $row[ 'lastSeen' ];
					$dt = new DateTime( "@$epoch", new DateTimeZone( 'UTC' ) );
					$dt->setTimezone( new DateTimeZone( 'America/Detroit' ) );
					echo '<tr';
					if ( isset( $_GET[ "id" ] ) ) {
						if ( $_GET[ "id" ] === $row[ "id" ] )echo ' id="scroll" style="background-color:rgba(0, 120, 255, .2)"';
					}
					echo '>';
					echo '<th scope="row">' . $row[ 'id' ] . '</td>';
					echo '<td>' . $row[ 'username' ] . '</td>';
					echo '<td>' . $row[ 'email' ] . '</td>';
					echo '<td>' . $row[ 'rank' ] . '</td>';
					echo '<td>' . $dt->format( 'F d, Y h:i a' ) . '</td>';
					if ( $row[ 'id' ] === $_SESSION[ 'id' ] || ( isAdmin( $_SESSION[ 'rank' ] ) && !isHigher( $row[ 'rank' ], $_SESSION[ 'rank' ] ) ) ) {
						echo "<td><button type='button' data-id='$row[id]' data-type='edit' data-toggle='modal' data-target='#editModal' class='btn btn-outline-warning'>Edit</button></td>";
						echo "<td><button type='button' data-id='$row[id]' data-type='delete' data-toggle='modal' data-target='#deleteModal' class='btn btn-outline-danger'>Delete</button></td>";
					}
					echo '</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<?php if(isAdmin($_SESSION['rank'])) { ?>
	<div class="text-center">
		<button type='button' data-type='add' data-toggle='modal' data-target='#userAddModal' class='btn btn-outline-primary'>Add User</button>
	</div>
	<?php }?>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="userDeleteModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="userDeleteModal">Confirm Delete</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			




			</div>
			<div class="modal-body">
				<p>You are about to delete the user <b id="modal-username"></b> forever.</p>
				<p><b id="modal-username"></b> was last seen on <b id="modal-seen"></b>
				</p>
				<p>Do you want to proceed?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger btn-ok" data-id="" id="deleteButton">Delete</button>
			</div>
		</div>
	</div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="userAddModal" tabindex="-1" role="dialog" aria-labelledby="profileModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="profileModal">Create a User Profile</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
			




			</div>
			<div class="modal-body">
				<div id="errorBox"></div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='userUserInput'>Username:</label>
					</div>
					<input autocomplete="nope" id='userUserInput' class='form-control' type='text'/>
				</div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='userEmailInput'>Email:</label>
					</div>
					<input id='userEmailInput' class='form-control' type='email'/>
				</div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='rankInput'>Rank:</label>
					</div>
					<select id="userRankInput" class="custom-select">
						<?php foreach(ranks_under($_SESSION['rank'], true) as $r){
						echo "<option value='$r'";
						if($r == array_values(array_slice(ranks_under($_SESSION['rank'], true), -1))[0])echo " selected ";
						echo ">$r</option>";
					} ?>
					</select>
				</div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='userPasswordInput'>Password:</label>
					</div>
					<input autocomplete="new-password" id='userPasswordInput' class='form-control' type='password'/>
				</div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='userPassword2Input'>Re-enter Password:</label>
					</div>
					<input autocomplete="new-password" id='userPassword2Input' class='form-control' type='password'/>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" id="submit1249">Create</button>
			</div>
		</div>
	</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="profileModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="profileModal">Update Profile</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
			




			</div>
			<div class="modal-body">
				<div id="errorBox"></div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='idInput'>ID:</label>
					</div>
					<input disabled id='idInput' class='form-control'/>
				</div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='userInput'>Name:</label>
					</div>
					<input id='userInput' class='form-control' type='text'/>
				</div>
				<div class="input-group mb-3" id="showPassword">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='passwordInput'>Password:</label>
					</div>
					<input id='passwordInput' class='form-control' type='password'/>
				</div>
				<div class="input-group mb-3" id="showPassword2">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='password2Input'>Re-enter Password:</label>
					</div>
					<input id='password2Input' class='form-control' type='password'/>
				</div>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='emailInput'>Email:</label>
					</div>
					<input id='emailInput' class='form-control' type='text'/>
				</div>
				<?php if(isAdmin($_SESSION['rank'])) {?>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='rankInput'>Rank:</label>
					</div>
					<select id="rankInput" class="custom-select">
						<?php foreach(ranks_under($_SESSION['rank'], true) as $r){
						echo "<option value='$r'>$r</option>";
					} ?>
					</select>
				</div>
				<?php } ?>
				<div class="input-group mb-3">
					<div class='input-group-prepend'>
						<label class='input-group-text' for='seenInput'>Last Seen:</label>
					</div>
					<input disabled id='seenInput' class='form-control' type='text'/>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" id="submit4364">Update</button>
			</div>
		</div>
	</div>
</div>

<script>
	function init() {
		$( '#submit1249' ).click( function () { // add user
			var name = $( '#userUserInput' ).val();
			var email = $( '#userEmailInput' ).val();
			var rank = $( '#userRankInput' ).val();
			var password = $( '#userPasswordInput' ).val();
			var password2 = $( '#userPassword2Input' ).val();
			$.ajax( {
				type: 'POST',
				url: 'addUser.php',
				data: {
					username: name,
					email: email,
					rank: rank,
					password: password,
					password2: password2
				},
				dataType: 'JSON',
				success: function ( data ) {
					console.log( "successfully created user" )
					if ( errorCheck( $( '#userAddModal' ), data[ 0 ] ) ) return;
					$( '#userAddModal' ).modal( 'hide' );
					let table = document.getElementsByTagName( "table" )[ 0 ];
					let row = table.insertRow( -1 );
					let cell = row.insertCell( -1 );
					cell.innerHTML = data[ 0 ].id;
					cell = row.insertCell( -1 );
					cell.innerHTML = data[ 0 ].username;
					cell = row.insertCell( -1 );
					cell.innerHTML = data[ 0 ].email;
					cell = row.insertCell( -1 );
					cell.innerHTML = data[ 0 ].rank;
					cell = row.insertCell( -1 );
					cell.innerHTML = data[ 0 ].lastSeen;
					cell = row.insertCell( -1 );
					cell.innerHTML = `<button type='button' data-id='${data[0].id}' data-type='edit' data-toggle='modal' data-target='#editModal' class='btn btn-outline-warning'>Edit</button>`;
					cell = row.insertCell( -1 );
					cell.innerHTML = `<button type='button' data-id='${data[0].id}' data-type='delete' data-toggle='modal' data-target='#deleteModal' class='btn btn-outline-danger'>Delete</button>`;
					addSuccess( "Successfully added user <strong>" + data[ 0 ].username + "</strong>!" );
					resetModal( $( "#userAddModal" ) );
				},
				error: function ( data ) {
					console.log( data )
				}
			} );
		} );

		$( '#deleteModal' ).on( 'show.bs.modal', function ( event ) { // Delete modal variable inject
			var modal = $( this );
			var button = $( event.relatedTarget );
			var id = button.data( 'id' );
			var tr = button.parent().parent().children();
			var username = tr.eq( 1 ).html();
			var seen = tr.eq( 4 ).html();
			modal.find( '.modal-body #modal-username' ).text( username );
			modal.find( '.modal-body #modal-seen' ).text( seen );
			modal.find( '.modal-footer #deleteButton' ).data( "id", id );
		} );

		$( '#deleteButton' ).on( "click", function () { // delete (inside of table)
			var id = $( this ).data( 'id' );
			$.ajax( {
				url: 'delete.php',
				type: 'POST',
				data: {
					id: id
				},
				success: function () {
					$( "table" ).find( "[data-id='" + id + "']" ).parent().parent().remove();
					$( '#deleteModal' ).modal( 'hide' );
				}
			} );
		} );

		$( '#editModal' ).on( 'show.bs.modal', function ( event ) { // Edit modal variable inject
			var modal = $( this );
			var button = $( event.relatedTarget );
			var type = button.data( 'type' );
			if ( type !== 'edit' ) return;
			var id = button.data( 'id' );
			var tr = button.parent().parent().children();
			var name = tr.eq( 1 ).html();
			var email = tr.eq( 2 ).html();
			var rank = tr.eq( 3 ).html();
			var seen = tr.eq( 4 ).html();
			modal.find( '.modal-title' ).text( 'Editing ' + name + "'s profile" );
			modal.find( '.modal-body #idInput' ).val( id );
			modal.find( '.modal-body #userInput' ).val( name );
			modal.find( '.modal-body #passwordInput' ).val( "" );
			modal.find( '.modal-body #password2Input' ).val( "" );
			modal.find( '.modal-body #rankInput' ).val( rank );
			modal.find( '.modal-body #emailInput' ).val( email );
			modal.find( '.modal-body #seenInput' ).val( seen );
		} );

		$( '#submit4364' ).click( function () { // edit (inside of modal)
			var id = $( '#idInput' ).val();
			var name = $( '#userInput' ).val();
			var rank = $( '#rankInput' ).val();
			var password = $( '#passwordInput' ).val();
			var password2 = $( '#password2Input' ).val();
			var email = $( '#emailInput' ).val();
			$.ajax( {
				type: 'POST',
				url: 'edit.php',
				data: {
					id: id,
					username: name,
					rank: rank,
					password: password,
					password2: password2,
					email: email
				},
				dataType: 'JSON',
				success: function ( data ) {
					if ( errorCheck( $( '#editModal' ), data[ 0 ] ) ) return; // if there are errors
					if ( data[ 0 ].reload == "true" ) location.reload();
					$( '#editModal' ).modal( 'hide' );
					var tr = $( "table" ).find( "[data-id='" + id + "']" ).parent().parent().children();
					tr.eq( 1 ).html( name );
					tr.eq( 2 ).html( email );
					tr.eq( 3 ).html( rank );
					addSuccess( "Successfully edited user <strong>" + name + "</strong>!" );
				}
			} );
		} );
		
	}

	$( document ).ready( function () {
		init();
	} );
	
	function rankSorter(a, b) {
		var ranks = "<?= implode("{`}",$available_ranks) ?>".split("{`}");

		var a_i = ranks.indexOf(a);
		var b_i = ranks.indexOf(b); 
		if (a_i > b_i) return 1;
		if (a_i < b_i) return -1;
		return 0;
	}
	
	function datesSorter(a, b) {
		if (new Date(a) < new Date(b)) return 1;
		if (new Date(a) > new Date(b)) return -1;
	 	return 0;
	}
</script>


<script>
	window.onload = ( () => {
		setTimeout( () => {
			document.getElementById( "scroll" ).scrollIntoView();
		}, 0 );
	} );
</script>