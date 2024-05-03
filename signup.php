<?php
require "DataBase.php";

$db = new DataBase();

if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['phoneNumber']) && isset($_POST['school'])) {
    if ($db->dbConnect()) {
        // Extract the first half of the fullname as fname
        $fullname = $_POST['fullname'];
        $fname = substr($fullname, 0, strpos($fullname, ' '));

        $school = $_POST['school'];
        $table = $school . "users";

        if ($db->signUp($table, $_POST['fullname'], $_POST['email'], $_POST['username'], $_POST['password'], $fname, $_POST['phoneNumber'], $school)) {
            // Redirect to login.html on successful sign-up
            header("Location: login.php");
            exit();
        } else {
            echo "Sign Up Failed";
        }
    } else {
        echo "Error: Database connection";
    }
} else {
    echo "All fields are required";
}






/*
require "DataBase.php";

$db = new DataBase();

if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
    if ($db->dbConnect()) {
        if ($db->signUp("users", $_POST['fullname'], $_POST['email'], $_POST['username'], $_POST['password'])) {
            // Redirect to login.html on successful sign-up
            header("Location: login.html");
            exit();
        } else {
            echo "Sign Up Failed";
        }
    } else {
        echo "Error: Database connection";
    }
} else {
    echo "All fields are required";
}



/*
require "DataBase.php";

$db = new DataBase();

if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
    if ($db->dbConnect()) {
        if ($db->signUp("users", $_POST['fullname'], $_POST['email'], $_POST['username'], $_POST['password'])) {
            echo "Sign Up Success";
        } else {
            echo "Sign up Failed";
        }
    } else {
        echo "Error: Database connection";
    }
} else {
    echo "All fields are required";
}



require "DataBase.php";
$db = new DataBase();
if (isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
    if ($db->dbConnect()) {
        if ($db->signUp("users", $_POST['fullname'], $_POST['email'], $_POST['username'], $_POST['password'])) {
            echo "Sign Up Success";
        } else echo "Sign up Failed";
    } else echo "Error: Database connection";
} else echo "All fields are required";
*/
?>