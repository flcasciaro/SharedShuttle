<?php include("functions.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <title>Personal Page</title>
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

//destroy possible signup session
if(isset($_SESSION['signup']))
    session_destroy();

define('capacity', 4);

$login = FALSE;
$book = FALSE;
$delete = FALSE;
$success = FALSE;

$conn = connectDB('localhost', 's252921', 'llaysing');

if (isset($_POST['uname']))
    $username = sanitizeString($conn, $_POST['uname']);
else
    $username = NULL;
if (isset($_POST['psw']))
    $pwd = sanitizeString($conn, $_POST['psw']);
else
    $pwd = NULL;

//recognize the source of the request (login button or book button or delete button)
if ($username != NULL && $pwd != NULL) {
    $login = TRUE;
} else {
    if (isset($_GET['goal']) && $_GET['goal'] == "delete") {
        $delete = TRUE;
    }
}

if ((isset($_GET["src1"]) || isset($_GET["src2"])) &&
    (isset($_GET["dst1"]) || isset($_GET["dst2"]))) {
    if ($_GET["src2"] != '')
        $src = $_GET["src2"];
    else
        $src = $_GET["src1"];
    if ($_GET["dst2"] != '')
        $dst = $_GET["dst2"];
    else
        $dst = $_GET["dst1"];

    $book = TRUE;
}

if ($login) {
    $success = validUser($conn, $username, $pwd);
    if ($success) {
        //create session
        session();
        $_SESSION['SSuser'] = $username;
        $_SESSION['previous'] = 'login';
    }
} else {
    //resume session
    session();
}

//manual handling of refreshed page
if (isset($_SESSION['previous']))
    $previous = $_SESSION['previous'];
else
    $previous = "";


if (!isset($_SESSION['SSuser'])) {
    $unauthorized = TRUE;
} else $unauthorized = FALSE;

if (!$unauthorized) {
    if (($login && $success) || $book || $delete || $previous == 'login')
        $bookPresence = checkBookPresence($conn, $_SESSION['SSuser']);

	$newWrong=FALSE;
    if ($book && !$bookPresence && $previous != 'bookSuccess') {
        $src = sanitizeString($conn, $src);
        $dst = sanitizeString($conn, $dst);
        $passengers = sanitizeString($conn, $_GET['passengers']);
        $success = book($conn, $_SESSION['SSuser'], $src, $dst, $passengers, capacity);
        if ($success){
			$bookPresence = TRUE;
			$_SESSION['previous'] = 'bookSuccess';
		}
        else{
			$_SESSION['previous'] = 'bookFail';
			$newWrong=TRUE;
		}
			
    }

    if ($delete && $bookPresence && $previous != 'deleteSuccess') {
        $success = deleteBook($conn, $_SESSION['SSuser']);
        if ($success){
			$bookPresence = FALSE;
			$_SESSION['previous'] = 'deleteSuccess';
		}
         else
			$_SESSION['previous'] = 'deleteFail';
    }
}
if (($login && !$success) || $unauthorized) {
    /*FAILED LOGIN HANDLER*/
    mysqli_close($conn);
    header('HTTP/1.1 307 temporary redirect');
    header('Location: index.php?msg=LoginFailed');
    exit; // IMPORTANT to avoid further output from the script
} else {
    /*LOGIN SUCCESS/BOOK/DELETE*/
    ?>
    <header>SHARED SHUTTLE</header>
    <body>
    <!-- LEFT-SIDE NAVIGATION BAR -->
    <div class="sidenav">
        <img src="shuttleimg.png" alt="shuttleimg" width="100px" height="100px"
             style="padding-top: 20px; padding-bottom: 20px; padding-left: 20px">
        <a href="#manBook">ManageBook</a>
        <a href="#logout">Logout</a>
        <a href="#contact">Contact</a>
    </div>
    <div class="main">
        <h1>USER PERSONAL PAGE</h1>
        <br><br>
        <script type=“text/javascript”>
        </script>
        <noscript>
            <div class="error">Warning: Your browser does not support or has disabled javascript!</div>
        </noscript>
        <br><br>
        <?php
        echo "<h2>Hello " . $_SESSION['SSuser'] . "</h2><br><br>";
        echo "<span style=color:green>";
        if ($login && $previous != 'login')
            echo "<h2>Login successful</h2><br><br>";
        if ($book && $success && $previous != 'bookSuccess')
            echo "<h2>Successful reservation</h2><br><br>";
        if ($delete && $success && $previous != 'deleteSuccess')
            echo "<h2>Your reservation was successfully deleted</h2><br><br>";
        echo "</span>";
        echo "<span style=color:red>";
        if (($book && !$success && ($previous != 'bookFail' && $previous != 'bookSuccess')) || $newWrong) {
            echo "<h2>Something went wrong with your reservation<br>
                Please check the remaining available number of passengers<br>
                OR the source/destination parameters</h2><br><br>";
        }
        if ($delete && !$success && ($previous != 'deleteFail' && $previous != 'deleteSuccess')) {
            echo "<h2>Something went wrong with the delete operation<br></h2>";
        }
        echo "</span>";
        if ($bookPresence)
            echo "<h3>You already have a <span style=color:red>reservation</span></h3>";
        else
            echo "<h3>You don't have any reservation</h3>";
        echo "<br><br>";
        echo "<h3>Current path configuration:</h3>";
        printPathEx($conn, $_SESSION['SSuser']);
        echo "<br><br>";

        if (!$bookPresence) {
            ?>
            <a name="manBook">
                <form name="book" action="personal.php" method="GET">
                    <h5>Select a source or insert a new one</h5>
                    <select name="src1">
                        <option value="">-</option>
                        <?php
                        $stops = retrieveStop($conn);
                        for ($i = 0; $i < sizeof($stops); $i++) {
                            echo "<option value=\"" . $stops[$i] . "\">" . $stops[$i] . "</option>";
                        }
                        ?>
                    </select>
                    <input type="text" placeholder="Enter new source" name="src2">
                    <h5>Select a destination or insert a new one</h5>
                    <select name="dst1">
                        <option value="">-</option>
                        <?php
                        for ($i = 0; $i < sizeof($stops); $i++) {
                            echo "<option value=\"" . $stops[$i] . "\">" . $stops[$i] . "</option>";
                        }
                        ?>
                    </select>
                    <input type="text" placeholder="Enter new destination" name="dst2">
                    <h5>Indicate the number of passengers: &nbsp;&nbsp;&nbsp;
                        <input type="number" placeholder="Passengers" name="passengers"
                               min="1" max="<?php echo capacity; ?>" step="1" style="width: 100px;">
                    </h5>

                    <br><br>
                    <button type="reset">Reset</button>&nbsp;&nbsp;&nbsp;<button type="submit"
                                                                                 onclick="return checkBookInput()">Book
                    </button>
                </form>
            </a>
            <br><br><br>
        <?php }
        if ($bookPresence) {
            ?>
            <a name="manBook">
                <button name="delete" onclick="location.href='personal.php?goal=delete'">Delete Book</button>
            </a>
        <?php } ?>
        <br><br><br><br>
        <a name="logout">
            <button name="logout" onclick="location.href='index.php'">Logout</button>
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
    </body>
    <?php
}
mysqli_close($conn);
?>

