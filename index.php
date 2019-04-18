<?php include("functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <title>Shared Shuttle Homepage</title>
    <script type="text/javascript" src="myjavascript.js"></script>
    <link href="mystyle.css" rel="stylesheet"/>
</head>
<?php
/*check if cookie are enabled with PHP
not done with navigator.cookieEnabled because JS can be disabled*/
setcookie("cookie_test", "cookie_value", time() + 3600);
if (!@cookieEnabled()) {
    ?>
    <script type="text/javascript"><!--
        alert("Cookie disabled, you cant'use the site");
        //--></script>
    <noscript>
        <div class="error">Warning: Cookie not enabled and your browser does not support or has disabled javascript!</div>
    </noscript>
    <?php
    exit(); //no render of the page
}
/*destroy session if any, useful for logout*/
session_start();
if (isset($_SESSION['SStime']) || isset($_SESSION['signup'])) {
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600 * 24,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();  // destroy session
}
?>
<header>SHARED SHUTTLE</header>
<body>
<!-- LEFT-SIDE NAVIGATION BAR -->
<div class="sidenav">
    <img src="shuttleimg.png" alt="shuttleimg" width="100px" height="100px"
         style="padding-top: 20px; padding-bottom: 20px; padding-left: 20px">
    <a href="#path">Path</a>
    <a href="#login">Login</a>
    <a href="#signup">Sign Up</a>
    <a href="#contact">Contact</a>
</div>
<div class="main">
    <br><br>
    <h1>HOMEPAGE</h1>
    <script type=“text/javascript”>
    </script>
    <noscript>
        <div class="error">Warning: Your browser does not support or has disabled javascript!</div>
    </noscript>
    <?php
    if (isset($_GET['msg']) && $_GET['msg'] == "LoginFailed") { ?>
        <div class="error">Wrong or missing credentials. Retry</div>
    <?php }
    if (isset($_GET['msg']) && $_GET['msg'] == "SessionTimeOut") { ?>
        <div class="error">Session Expired. Redo Login</div>
    <?php } ?>
    <br><br>
    <!-- SHOW CURRENT PATH -->
    <a name="path">
        <h3>Current path configuration:</h3>

        <?php
        $conn = connectDB('localhost', 's252921', 'llaysing');
        printPath($conn);
        mysqli_close($conn);
        ?>
    </a>
    <!-- LOGIN form -->
    <a name="login"><br><br>
        <h3>Login to use our booking services</h3></span>
        <div class="container">
            <form action="personal.php" id="login" name="login" method="post">
                <label for="uname"><b>Username</b></label>
                <input type="email" placeholder="Enter Username" name="uname" required>
                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>
                <br><br>
                <button type="submit"
                ">Login</button>
                <br><br><br><br>
            </form>
        </div>
    </a>


    <!-- SIGN UP form -->
    <a name="signup"><br><br>
        <h3>Create a new account to access our booking services</h3>
        <div class="container">
            <form action="signup.php" id="signup" name="signup" method="post">
                <label for="uname"><b>Username</b></label>
                <input type="email" placeholder="Enter Username" name="uname" required>
                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>
                <br><br>
                <!--added return before checkSignup() in order to avoid redirection in case of wrong inputs-->
                <button type="submit" onclick="return checkSignup()">Sign up</button>
                <br><br><br><br><br><br>
            </form>
        </div>
    </a>

    <br><br>
    <br><br>
    <br><br>
    <br><br>
    <!-- CONTACT section -->
    <a name="contact">
        <h5>This website is mantained by Francesco Lorenzo Casciaro for the DP1 Assignment at Polito</h5>
    </a>
</div> <!-- close main container -->

<?php
if (isset($_GET['msg']) && $_GET['msg'] == "LoginFailed") { ?>
    <script type="text/javascript"><!--
        window.onload= function () {
            alert("Login Failed. Wrong or missing credentials");
        }
        //--></script>
<?php }
if (isset($_GET['msg']) && $_GET['msg'] == "SessionTimeOut") { ?>
    <script type="text/javascript"><!--
        window.onload = function () {
            alert("Session expired after 120 seconds of inactivity");
        }
        //--></script>
<?php } ?>
</body>
