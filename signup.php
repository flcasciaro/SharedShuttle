<?php include("functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <title>Sign Up</title>
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
//HTTP->HTTPS REDIRECTION, HANDLING SEPARATELY GET AND POST REQUEST
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        echo "<form name='fr' action='" . $redirect . "' method='POST'>";
        echo "<input type='hidden' name='uname' value='" . $_POST["uname"] . "'>";
        echo "<input type='hidden' name='psw' value='" . $_POST["psw"] . "'>";
        echo "</form>";
        echo "<script type = 'text/javascript' > document . fr . submit(); </script >";
    } else {
        //GET REQUEST
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
    }
    exit();
}
session_start();
$_SESSION['signup'] = true;
?>
<header>SHARED SHUTTLE</header>
<body>
<!-- LEFT-SIDE NAVIGATION BAR -->
<div class="sidenav">
    <img src="shuttleimg.png" alt="shuttleimg" width="100px" height="100px"
         style="padding-top: 20px; padding-bottom: 20px; padding-left: 20px">
    <a href="#signup">Signup</a>
    <a href="#contact">Contact</a>
</div>
<div class="main">
    <h1>SIGN UP PAGE</h1>
    <br><br>
    <script type=“text/javascript”>
    </script>
    <noscript>
        <div class="error">Warning: Your browser does not support or has disabled javascript!</div>
    </noscript>
    <br><br>

    <?php
    //first time on the page or trying to signup with a different username/password
    if (!isset($_SESSION['previous']) ||
        isset($_SESSION['tryUser']) && ($_SESSION['tryUser'])!=$_POST['uname'] ||
        isset($_SESSION['tryPwd']) && ($_SESSION['tryPwd'])!=$_POST['psw']) {

        $conn = connectDB('localhost', 's252921', 'llaysing');

        $fail = FALSE;

        /*username and password validation*/
        /*sanitize in order to avoid code injection injection of username*/
        if (isset($_POST['uname']))
            $username = sanitizeString($conn, $_POST['uname']);
        else
            $username = NULL;
        if (isset($_POST['psw']))
            $pwd = sanitizeString($conn, $_POST['psw']);
        else
            $pwd = NULL;

        $_SESSION['tryUser']=$username;
        $_SESSION['tryPwd']=$pwd;

        //in case of GET request
        if ($username == NULL || $pwd == NULL) {
            header('HTTP/1.1 307 temporary redirect');
            header('Location: home.php?msg=WrongCredential');
            exit; // IMPORTANT to avoid further output from the script
        }

        if (checkUsername($conn, $username))
            $correctUsername = availableUsername($conn, $username);
        else $correctUsername = FALSE;

        /*password validation*/
        $correctPassword = checkPassword($conn, $pwd);

        if ($correctUsername && $correctPassword) {
            $query = "INSERT INTO credentials(Username,Password) VALUES ('" . $username . "','" . md5($pwd) . "')";
            if (mysqli_query($conn, $query))
                $_SESSION['previous'] = 'ok';
            else {
                $_SESSION['previous'] = 'fail';
                echo "<div class='error'>Something went wrong with the DB</div>";
            }
            mysqli_close($conn);
        } else
            $_SESSION['previous'] = 'failnow';
    }

    //at this point $_SESSION['previous'] is always set

    if ($_SESSION['previous'] == 'ok') {
        ?><h3>Signup success</h3>
        <br><br><br>
        <h3>Now you can login</h3>
        <br><br>
        <a name="signup">
            <div class="container">
                <form action="personal.php" id="login" name="login" method="post">
                    <label for="uname"><b>Username</b></label>
                    <input type="email" value="<?php echo $_POST["uname"] ?>" name="uname" required>
                    <label for="psw"><b>Password</b></label>
                    <input type="password" value="<?php echo $_POST["psw"] ?>" name="psw" required>
                    <br><br>
                    <button type="submit">Login</button>
                    <br><br><br><br>
                </form>
            </div>
        </a>
    <?php } else {
        if ($_SESSION['previous'] == 'failnow') {
            ?>
            <br><br><br>
            <?php
            if (!$correctUsername)
                echo "<div class='error'>The username is already taken or is an invalid mail address</div>";
            if (!$correctPassword)
                echo "<div class='error'>Password must contain at least one lower case character <br> and one between upper case character and digit</div>";
        }

        $_SESSION['previous'] = 'fail'; ?>
        <h3>Sign up failed. Retry:</h3>
        <br>
        <a name="signup">
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
    <?php } ?>

    <br>
    <br><br>
    <br><br>
    <button name="goback" onclick="location.href='index.php'">Homepage</button>
    <br><br>
    <br><br>
    <!-- CONTACT section -->
    <a name="contact">
        <h5>This website is mantained by Francesco Lorenzo Casciaro for the DP1 Assignment at Polito</h5>
    </a>


</div> <!-- close main container -->
</body>