<?php
// sanitize input string
function sanitizeString($conn, $var)
{
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripcslashes($var);
    return mysqli_real_escape_string($conn, $var);
}

function connectDB($host, $user, $pwd)
{
    $conn = mysqli_connect($host, $user, $pwd);
    if (!$conn) {
        die('Connect error ('
            . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
    }
    if (!mysqli_select_db($conn, "s252921"))
        die("Error when selecting the db: " . mysqli_error($conn));
    return $conn;
}

//list path and total reservation for each path
function printPath($conn)
{
    $query = "SELECT * FROM path ORDER BY Stop ASC";
    $res = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($res);
    if ($row != NULL) {
        while (($row2 = mysqli_fetch_array($res)) != NULL) {
            $sql = "SELECT * FROM reservations 
                    WHERE Source <= '" . $row["Stop"] . "' 
                    AND Destination >= '" . $row2["Stop"] . "'";
            $res2 = mysqli_query($conn, $sql);
            $total = 0;
            while (($row3 = mysqli_fetch_array($res2)) != NULL) {
                $total += $row3["Passengers"];
            }
            if ($total > 0) {
                echo "<h4>" . $row["Stop"] . " ";
                echo "<img src='arrow.png' alt='arrow' width='30px' height='20px' style='margin-bottom: -3px'>";
                echo " " . $row2["Stop"] . "   Total: " . $total . "<br></h4>";
            } else {
                echo "<h4>" . $row["Stop"] . " ";
                echo "<img src='arrow.png' alt='arrow' width='30px' height='20px' style='margin-bottom: -3px'>";
                echo " " . $row2["Stop"] . "   Total: 0 (Empty) <br></h4>";
            }
            $row = $row2;
            mysqli_free_result($res2);
        }

    }
    mysqli_free_result($res);
}

//printPath extended for the user personal page format
function printPathEx($conn, $user)
{
    $user = sanitizeString($conn, $user);
    $sql = "SELECT * FROM reservations WHERE Username = '" . $user . "'";
    $resp = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($resp);
    if ($row != NULL) {
        $src = $row["Source"];
        $dst = $row["Destination"];
    } else {
        //no red string
        $src = NULL;
        $dst = NULL;
    }
    mysqli_free_result($resp);
    $query = "SELECT * FROM path ORDER BY Stop ASC";
    $res = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($res);
    if ($row != NULL) {
        while (($row2 = mysqli_fetch_array($res)) != NULL) {
            $sql = "SELECT * FROM reservations 
                    WHERE Source <= '" . $row["Stop"] . "' 
                    AND Destination >= '" . $row2["Stop"] . "'";
            $res2 = mysqli_query($conn, $sql);
            $total = 0;
            $users = "";
            $row3 = mysqli_fetch_array($res2);
            while (1) {
                $total += $row3["Passengers"];
                if ($row3["Username"] == $user) {
                    $users .= "<span style=color:red>";
                }
                $users .= $row3["Username"] . " (" . $row3["Passengers"] . " Passengers)";
                if ($row3["Username"] == $user) {
                    $users .= "</span>";
                }
                $row3 = mysqli_fetch_array($res2);
                if ($row3 == NULL)
                    break;
                else
                    $users .= ", ";
            }
            if ($total > 0) {
                echo "<h4>";
                if ($row["Stop"] == $src) {
                    echo "<span style=color:red>";
                }
                echo $row["Stop"];
                if ($row["Stop"] == $src) {
                    echo "</span>";
                }
                echo " "."<img src='arrow.png' alt='arrow' width='30px' height='20px' style='margin-bottom: -3px'>"." ";
                if ($row2["Stop"] == $dst) {
                    echo "<span style=color:red>";
                }
                echo $row2["Stop"];
                if ($row2["Stop"] == $dst) {
                    echo "</span>";
                }
                echo "   Total: " . $total . "   { ";
                echo $users;
                echo " }<br>";
            } else {
                echo "<h4>" . $row["Stop"];
                echo " " . "<img src='arrow.png' alt='arrow' width='30px' height='20px' style='margin-bottom: -3px'>" . " ";
                echo $row2["Stop"] . "   Total: 0 (Empty) <br></h4>";
            }
            $row = $row2;
            mysqli_free_result($res2);
        }

    }
    mysqli_free_result($res);
}

function availableUsername($conn, $username)
{
    $user = sanitizeString($conn, $username);
    $query = "SELECT * FROM credentials WHERE Username = '" . $username . "'";
    $res = mysqli_query($conn, $query);
    if (mysqli_num_rows($res) == 0) {
        mysqli_free_result($res);
        return TRUE;
    } else {
        mysqli_free_result($res);
        return FALSE;
    }
}

//correct username format:   xxx@zzz.yyy
function checkUsername($conn, $username)
{
    $user = sanitizeString($conn, $username);
    $firstSplit = explode("@", $user);
    if (sizeof($firstSplit) != 2) return FALSE;
    $secondSplit = explode(".", $firstSplit[1]);
    if (sizeof($secondSplit) != 2) return FALSE;
    return TRUE;
}


/*CHECK: password must contain at least a lowercase alphabetic character,
and at least another character which must be either an
uppercase alphabetic character or a digit*/
function checkPassword($conn, $pwd)
{
    $lower = FALSE;
    $upperOrDigit = FALSE;
    $pwd = sanitizeString($conn, $pwd);
    for ($i = 0; $i < strlen($pwd); $i++) {
        if (ctype_lower($pwd[$i]))
            $lower = TRUE;
        if (ctype_upper($pwd[$i]) || ctype_digit($pwd[$i]))
            $upperOrDigit = TRUE;
    }
    return $lower && $upperOrDigit;
}

// check given username and password
// return true if user exists with given username and password
function validUser($conn, $user, $password)
{
    $user = sanitizeString($conn, $user);
    $password = sanitizeString($conn, $password);
    $query = "SELECT Password FROM credentials WHERE Username = '" . $user . "'";
    $resp = mysqli_query($conn, $query);
    if (!$resp)
        die("Error in query: " . $query . "<br>" . mysqli_error($conn));
    if (mysqli_num_rows($resp) == 0)
        return FALSE;
    $row = mysqli_fetch_array($resp);
    $res = (md5($password) == $row[0]);
    mysqli_free_result($resp);
    return ($res);
}

/*useful function for understand if cookies are enabled or not*/
function cookieEnabled()
{
    if ($_COOKIE["cookie_test"] == "cookie_value") {
        return TRUE;
    } else {
        return FALSE;
    }
}

function session()
{
    define("EXPIRE_TIME", 120);
    session_start();
    $t = time();
    $diff = 0;
    //$new=false;
    if (isset($_SESSION['SStime'])) {
        $t0 = $_SESSION['SStime'];
        $diff = ($t - $t0);  // inactivity period
    } /*else {
        $new=true;
    }*/
    //if ($new || ($diff > 10)) { // new or with inactivity period too long//
    if ($diff > EXPIRE_TIME) {
        //session_unset(); 	// Deprecated
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
        // redirect client to login page
        header('HTTP/1.1 307 temporary redirect');
        header('Location: index.php?msg=SessionTimeOut');
        exit(); // IMPORTANT to avoid further output from the script
    } else {
        $_SESSION['SStime'] = time(); /* update time */
    }
}

function checkBookPresence($conn, $user)
{
    $user = sanitizeString($conn, $user);
    $query = "SELECT * FROM reservations WHERE Username = '" . $user . "'";
    $resp = mysqli_query($conn, $query);
    if (!$resp)
        die("Error in query: " . $query . "<br>" . mysqli_error($conn));
    if (mysqli_num_rows($resp) == 0) {
        mysqli_free_result($resp);
        return FALSE;
    } else {
        mysqli_free_result($resp);
        return TRUE;
    }
}

function presentStop($conn, $stop)
{
    $query = "SELECT Stop FROM path";
    $res = mysqli_query($conn, $query);
    while (($row = mysqli_fetch_array($res)) != NULL) {
        if ($row['Stop'] == $stop) {
            mysqli_free_result($res);
            return TRUE;
        }
    }
    mysqli_free_result($res);
    return FALSE;
}


function book($conn, $user, $src, $dst, $passengers, $capacity)
{
    $user = sanitizeString($conn, $user);
    $src = sanitizeString($conn, $src);
    $dst = sanitizeString($conn, $dst);
    $passengers = sanitizeString($conn, $passengers);
    if ($src >= $dst)
        return FALSE;
    //start transaction
    mysqli_autocommit($conn, FALSE);
    $query = "INSERT reservations(Username,Source,Destination,Passengers)
              VALUES ('" . $user . "','" . $src . "','" . $dst . "','" . $passengers . "')";
    if (!mysqli_query($conn, $query)) {
        mysqli_rollback($conn);
        return FALSE;
    }
    //add possible new stops, IGNORE for no error returned in case of duplicate key
    if (!presentStop($conn, $src)) {
        $sql = "INSERT INTO path(Stop) VALUES('" . $src . "')";
        if (!mysqli_query($conn, $sql)) {
            mysqli_rollback($conn);
            return FALSE;
        }
    }
    if (!presentStop($conn, $dst)) {
        $sql = "INSERT INTO path(Stop) VALUES('" . $dst . "')";
        if (!mysqli_query($conn, $sql)) {
            mysqli_rollback($conn);
            return FALSE;
        }
    }
    //check capacity constraint
    //for update OPTION lock tuples while checking constraint
    $query = "SELECT * FROM path ORDER BY Stop ASC FOR UPDATE";
    $res = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($res);
    if ($row != NULL) {
        while (($row2 = mysqli_fetch_array($res)) != NULL) {
            $sql = "SELECT * FROM reservations 
                    WHERE Source <= '" . $row["Stop"] . "' 
                    AND Destination >= '" . $row2["Stop"] . "'
                    FOR UPDATE";
            $res2 = mysqli_query($conn, $sql);
            $total = 0;
            while (($row3 = mysqli_fetch_array($res2)) != NULL) {
                $total += $row3["Passengers"];
            }
            if ($total > $capacity) {
                mysqli_free_result($res);
                mysqli_free_result($res2);
                mysqli_rollback($conn);
                return FALSE;
            }
            $row = $row2;
            mysqli_free_result($res2);
        }

    }
    mysqli_free_result($res);
    //end transaction
    mysqli_commit($conn);
    return TRUE;
}

function deleteBook($conn, $user)
{
    //check reservation presence
    $user = sanitizeString($conn, $user);
    $sql = "SELECT * FROM reservations WHERE Username = '" . $user . "' FOR UPDATE";
    $resp = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($resp);
    mysqli_free_result($resp);
    if ($row == NULL)
        return FALSE;
    //start transaction
    mysqli_autocommit($conn, false);
    //delete reservation
    $sql = "DELETE FROM reservations WHERE Username = '" . $user . "'";
    if (!mysqli_query($conn, $sql)) {
        mysqli_rollback($conn);
        return FALSE;
    }
    //delete possible useless stop (ex. boundary empty, more than one empty adjacent, no departure/arrival stop)
    if (!removeUselessStop($conn)) {
        mysqli_rollback($conn);
        return FALSE;
    }
    //end transaction
    mysqli_commit($conn);
    return TRUE;
}

function removeUselessStop($conn)
{
    $query = "SELECT Stop FROM path 
              WHERE Stop NOT IN (
                    SELECT Source FROM reservations
              )
              AND Stop NOT IN (
                    SELECT Destination FROM reservations
              ) FOR UPDATE";
    $res = mysqli_query($conn, $query);
    while (($row = mysqli_fetch_array($res)) != NULL) {
        $sql = "DELETE FROM path WHERE Stop = '" . $row["Stop"] . "'";
        if (!mysqli_query($conn, $sql)) {
            mysqli_free_result($res);
            return FALSE;
        }
    }
    mysqli_free_result($res);
    return TRUE;
}

function retrieveStop($conn)
{
    $sql = "SELECT Stop FROM path ORDER BY Stop ASC";
    $resp = mysqli_query($conn, $sql);
    $i = 0;
    while (($row = mysqli_fetch_array($resp)) != NULL) {
        $stops[$i] = $row["Stop"];
        $i++;
    }
    return $stops;
}

