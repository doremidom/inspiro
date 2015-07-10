<?php

// header('Content-type: application/json');
if($_POST) {
	$username   = $_POST['username'];
	$password   = $_POST['password'];
	$email		= $_POST['email'];
	$fullname	= $_POST['fullname'];

	// $c_password = $_POST['c_password'];

	if($_POST['username']) {
		// if ( $password == $c_password ) {

			// Get database login credentials
			$db_info = file_get_contents('db_info.json');
			$db_json = json_decode($db_info);

			$db_name     = $db_json->name;
			$db_user     = $db_json->user;
			$db_password = $db_json->password;
			$server_url  = $db_json->url;

			$mysqli = new mysqli($server_url, $db_user, $db_password, $db_name);

			/* check connection */
			if (mysqli_connect_errno()) {
				error_log("Connect failed: " . mysqli_connect_error());
				echo '{"success":0,"error_message":"' . mysqli_connect_error() . '"}';
			} else {
				$stmt = $mysqli->prepare("INSERT INTO users (username, password, email, fullname) VALUES (?, ?, ?, ?)");
				$password = md5($password);
				$stmt->bind_param('ss', $username, $password, $email, $fullname);

				/* execute prepared statement */
				$stmt->execute();

				if ($stmt->error) {error_log("Error: " . $stmt->error); }

				$success = $stmt->affected_rows;

				/* close statement and connection */
				$stmt->close();

				/* close connection */
				$mysqli->close();
				error_log("Success: $success");

				if ($success > 0) {
					error_log("User '$username' created.");
					echo '{"success":1}';
				} else {
					echo '{"success":0,"error_message":"Username exists."}';
				}
			}
		// } else {
		// 	echo '{"success":0,"error_message":"Passwords does not match."}';
		// }
	} else {
		echo '{"success":0,"error_message":"Invalid username."}';
	}
}else {
	echo '{"success":0,"error_message":"Invalid data :("}';
}
?>
