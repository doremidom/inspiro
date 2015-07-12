<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1"> 

        <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="style.css" />

        <link rel="stylesheet" href="themes/inspiro.min.css" />
        <link rel="stylesheet" href="themes/jquery.mobile.icons.min.css" />
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile.structure-1.4.5.min.css" />
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

        <title>Inspiro</title>
    </head>
    <body>

        <div id="main" data-role="page">
            <img id="logo" src="logo.png">
            <?php
            // var_dump($_POST);
            // header('Content-type: application/json');
           $signup_html = '<form name="signup" action="signup.php" method="post">
                            <input type="text" name="fullname" id="fullname" placeholder="Full Name"  />
                            <input type="text" name="email" id="email" placeholder="Email"  />
                            <input type="text" name="username" id="username" placeholder="Username"  />
                            <input type="password" name="password" id="password" placeholder="Password"  />
                            <!-- <input type="text" name="name" id="portfolio" placeholder="Portfolio link"  /> -->
                            <button name="submit" type="submit">Sign Up</button>
                            </form>
                            ';

            $success_html = '   <div>
                                Success! Log in with your new username and password <a href="index.php">here</a>.
                                </div>
                            ';
            $exists_html = '    <div>
                                    Sorry, this username already exists.
                                </div>
                                <br>
                           ';
            $error_html = '     <div>
                                    Looks like you didn\'t fill in all the fields. Please try again.
                                </div>
                                <br>
                          ';
            if($_POST) {
                $username   = $_POST['username'];
                $password   = $_POST['password'];
                $email      = $_POST['email'];
                $fullname   = $_POST['fullname'];

                // $c_password = $_POST['c_password'];

                if($username && $password && $email && $fullname) {
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
                            // echo $username;
                            $stmt->bind_param('ssss', $username, $password, $email, $fullname);

                            /* execute prepared statement */
                            $stmt->execute();

                            if ($stmt->error) {echo("Error: " . $stmt->error); }

                            $success = $stmt->affected_rows;

                            /* close statement and connection */
                            $stmt->close();

                            /* close connection */
                            $mysqli->close();
                            error_log("Success: $success");

                            if ($success > 0) {
                                error_log("User '$username' created.");
                                // echo '{"success":1}';
                                echo $success_html;
                            } else {
                                // echo '{"success":0,"error_message":"Username exists."}';
                                // echo 'jlkj';
                                echo $exists_html;
                                echo $signup_html;
                            }
                        }
                    // } else {
                    //  echo '{"success":0,"error_message":"Passwords does not match."}';
                    // }
                } else {
                    // echo '{"success":0,"error_message":"Invalid username."}';
                    echo $error_html;
                    echo $signup_html;
                }
            }
            else {
                // echo '{"success":0,"error_message":"Invalid data :("}';
                echo $signup_html;
            }
            ?>

            <!-- <h2>Signup</h2> -->
            <!-- <a href="signup.html" data-role="button">Signup</a> -->
        </div>
    </body>
</html>