<?php

header('Content-type: application/json');
// header('Access-Control-Allow-Origin, Access-Control-Allow-Methods and Access-Control-Allow-Headers');

if($_POST) {
	$username   = $_POST['username'];
	$password   = $_POST['password'];

	if($username && $password) {

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
				if ($stmt = $mysqli->prepare("SELECT username FROM users WHERE username = ? and password = ?")) {

					$password = md5($password);

					/* bind parameters for markers */
					$stmt->bind_param("ss", $username, $password);

					/* execute query */
					$stmt->execute();

					/* bind result variables */
					$stmt->bind_result($id);

					/* fetch value */
					$stmt->fetch();

					/* close statement */
					$stmt->close();
				}

				/* close connection */
				$mysqli->close();

				if ($id) {
					error_log("User $username: password match.");
					echo '{"success":1}';
				} else {
					error_log("User $username: password doesn't match.");
					echo '{"success":0,"error_message":"Invalid username/password combination"}';
				}
			}
	} else {
		echo '{"success":0,"error_message":"Invalid username/password combination"}';
	}
}else {
	echo '{"success":0,"error_message":"Invalid data :("}';
}
?>
