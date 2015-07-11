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
        <!-- <div id="wrapper"> -->
        <div id="main" data-role="page">
            <!-- <h1>inspiro</h1> -->
            <img id="logo" src="logo.png">
            <?php
                $login_html = '         <form name="login" action="index.php" method="post">
                                        <input type="text" name="username" id="username" placeholder="Username/Email"  />
                                        <input type="password" name="password" id="password" placeholder="Password"  />
                                        <button type="submit" name="submit" value="login">Login</button>
                                        </form>
                                        <br>
                                        Need an account? Sign up <a href="signup.html">here</a>
                              ';

                $profile_html = '       <div>
                                            Welcome!
                                        </div>

                                ';

                $error_html = '     Sorry, we couldn\'t find that username/password combination. Please try again.
                                    <br>
                              ';
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

                                    /* bind paramaters for markers */
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
                                    // echo '{"success":1}';
                                    var_dump($id);
                                    echo $profile_html;
                                } else {
                                    error_log("User $username: password doesn't match.");
                                    // echo '{"success":0,"error_message":"Invalid username/password combination"}';
                                    echo $error_html.$login_html;
                                }
                            }
                    } else {
                        // echo '{"success":0,"error_message":"Invalid username/password combination"}';
                        echo $error_html.$login_html;
                    }
                } else {
                    // echo '{"success":0,"error_message":"Invalid data :("}';
                    echo $login_html;
                }
            ?>

            <!-- <form name="login" action="index.php" method="post" target="_blank">
            <input type="text" name="username" id="username" placeholder="Username/Email"  />
            <input type="password" name="password" id="password" placeholder="Password"  />
            <button type="submit" name="submit" value="login" data-ajax="false">Login</button>
            </form>
            <br>
            Need an account? Sign up <a href="signup.html">here</a> -->
        </div>
        <!-- </div> -->
    </body>
</html>