<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1"> 
        <meta name="apple-mobile-web-app-capable" content="yes" />

        <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="style.css" />

        <link rel="stylesheet" href="themes/inspiro.min.css" />
        <link rel="stylesheet" href="themes/jquery.mobile.icons.min.css" />
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile.structure-1.4.5.min.css" />
        <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
        <script>
            // // scrollTo hack to hide address bar
            // window.addEventListener("load", function() {
            //     setTimeout(function() {
            //         window.scrollTo(0, 1);
            //     }, 0);
            // });
    
            // // Alert coming soon!
            // $(document).on("pageinit", function() {
            //     $(".soonbutton").on("click", function() {
            //         alert("Coming soon!");
            //     });
            // });

        </script>


        <title>Inspiro</title>
    </head>
    <body>
        <!-- <div id="wrapper"> -->
        <div id="main" data-role="page">
            <!-- <h1>inspiro</h1> -->
            <!-- <img id="logo" src="logo.png"> -->
            <?php
                $header_html = '<img id="logo" src="logo.png">';
                $login_html = '
                                        <form name="login" action="index.php" method="post">
                                        <input type="text" name="username" id="username" placeholder="Username/Email"  />
                                        <input type="password" name="password" id="password" placeholder="Password"  />
                                        <button type="submit" name="submit" value="login">Login</button>
                                        </form>
                                        <br>
                                        Need an account? Sign up <a href="signup.php">here</a>
                              ';

                $profile_html = '
                                        <!-- HEADER -->
                                        <div class="headergrid ui-grid-b">
                                            <div class="ui-block-a">
                                                <img class="profilepicture-block" src="user.png">
                                                <span class="padder">Welcome, David!</span>
                                                <!-- <a href="index.php" class="borderless rightmost ui-btn ui-icon-home ui-btn-icon-notext"></a> -->
                                            </div>
                                            <div class="ui-block-b">
                                                <img class="logo-block" src="logo.png">
                                            </div>
                                            <div class="ui-block-c">
                                                <a href="index.php" class="borderless rightmost ui-btn ui-icon-bars ui-btn-icon-notext"></a>
                                                <!-- <a href="index.php" class="borderless rightmost ui-btn ui-icon-mail ui-btn-icon-notext"></a> -->
                                            </div>
                                        </div>

                                        <!--WELCOME-->
                                        <div>
                                            Welcome, %s!
                                        </div>

                                        <br><br><br>

                                        <!-- CONTENT -->
                                        <div class="ui-grid-a">
                                            <div class="ui-block-a">
                                                <a href="people.html" data-ajax="false" data-role="button">Find People</a>
                                            </div>
                                            <div class="ui-block-b">
                                                <a href="#comingSoon" class="soonbutton" data-role="button" data-rel="popup" data-position-to="window" data-transition="pop">Find Projects</a>
                                            </div>
                                        </div>
                                        
                                        <div data-role="popup" id="comingSoon" data-theme="a">
                                            <p>Coming soon!</p>
                                        </div>
                                ';

                $error_html = '     <div>
                                        Sorry, we couldn\'t find that username/password combination. Please try again.
                                    </div>
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
                                if ($stmt = $mysqli->prepare("SELECT id, email, fullname, portfolio_link FROM users WHERE username = ? and password = ?")) {

                                    $password = md5($password);

                                    /* bind paramaters for markers */
                                    $stmt->bind_param("ss", $username, $password);

                                    /* execute query */
                                    $stmt->execute();

                                    /* bind result variables */
                                    $stmt->bind_result($id, $email, $fullname, $portfolio_link);

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
                                    echo sprintf($profile_html, $fullname);
                                } else {
                                    error_log("User $username: password doesn't match.");
                                    // echo '{"success":0,"error_message":"Invalid username/password combination"}';
                                    echo $header_html.$error_html.$login_html;
                                }
                            }
                    } else {
                        // echo '{"success":0,"error_message":"Invalid username/password combination"}';
                        echo $header_html.$error_html.$login_html;
                    }
                } else {
                    // echo '{"success":0,"error_message":"Invalid data :("}';
                    echo $header_html.$login_html;
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