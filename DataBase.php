<?php
require "DataBaseConfig.php";
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
use Twilio\Rest\Client;


class DataBase
{
    public $connect;
    public $data;
    private $sql;
    protected $servername;
    protected $username;
    protected $password;
    protected $databasename;

    public function __construct()
    {
        $this->connect = null;
        $this->data = null;
        $this->sql = null;
        $dbc = new DataBaseConfig();
        $this->servername = $dbc->servername;
        $this->username = $dbc->username;
        $this->password = $dbc->password;
        $this->databasename = $dbc->databasename;
    }

    function dbConnect()
    {
    $this->connect = mysqli_connect($this->servername, $this->username, $this->password, $this->databasename);

    // Check connection
    if (!$this->connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $this->connect;
    }


    /*function prepareData($data)
    {
        // Check if the connection is established before calling mysqli_real_escape_string
        if ($this->connect) {
            return mysqli_real_escape_string($this->connect, stripslashes(htmlspecialchars($data)));
        } else {
            // Handle the case where the connection is not established
            die("Error: Database connection not Established.");
        }
    }*/

    public function dbQuery($query) {
        // Execute the query using mysqli_query
        return mysqli_query($this->connect, $query);
    }

    function prepareData($data)
    {
        // Check if the connection is established before calling mysqli_real_escape_string
        if ($this->connect && is_object($this->connect)) {
          return mysqli_real_escape_string($this->connect, stripslashes(htmlspecialchars($data)));
        } else {
            // Handle the case where the connection is not established
            die("Error: Database connection not established.");
            echo "data $data";
        }
    }

    function logIn($table, $username, $password)
    {
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $this->sql = "select * from " . $table . " where username = '" . $username . "'";
        $result = mysqli_query($this->connect, $this->sql);
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) != 0) {
            $dbusername = $row['username'];
            $dbpassword = $row['password'];
            if ($dbusername == $username && password_verify($password, $dbpassword)) {
                $login = true;
            } else $login = false;
        } else $login = false;

        return $login;
    }

    /*function signUp($table, $fullname, $email, $username, $password, $fname, $phoneNumber, $school)
    {
        $fullname = $this->prepareData($fullname);
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $phoneNumber = $this->prepareData($phoneNumber);
        $email = $this->prepareData($email);
        $fname = $this->prepareData($fname); // New line for fname
        $password = password_hash($password, PASSWORD_DEFAULT);
        $school = $this->prepareData($school);
        $this->sql =
            "INSERT INTO " . $table . " (fullname, username, password, email, fname, phoneNumber, school) VALUES ('" . $fullname . "','" . $username . "','" . $password . "','" . $email . "','" . $fname . "','" . $phoneNumber . "','" . $school . "')";
        if (mysqli_query($this->connect, $this->sql)) {
            return true;
    }   else return false;
}*/
    function signUp($table, $fullname, $email, $username, $password, $fname, $phoneNumber, $school)
    {
        $fullname = $this->prepareData($fullname);
        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $phoneNumber = $this->prepareData($phoneNumber);
        $email = $this->prepareData($email);
        $fname = $this->prepareData($fname);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $school = $this->prepareData($school);

        // Call signUp2 function directly, it's not a method of the same class
        $this->signUp2($fullname, $username, $email, $school);

        $table = $school . "users"; // Ensure $table is constructed properly
        $this->sql = "INSERT INTO " . $table . " (fullname, username, password, email, fname, phoneNumber, school) VALUES ('" . $fullname . "','" . $username . "','" . $password . "','" . $email . "','" . $fname . "','" . $phoneNumber . "','" . $school . "')";
        echo "SQL Query: " . $this->sql . "<br>";

        if (mysqli_query($this->connect, $this->sql)) {
            return true;
        } else {
            // Error handling: Output MySQL error message
            echo "Error: " . mysqli_error($this->connect) . "<br>";
            return false;
        }
    }

    // Define signUp2 as a separate function
    function signUp2($fullname, $username, $email, $school)
    {
        // Assuming $this->connect is accessible in the global scope or passed as an argument
        $connect = $this->connect;
        $sql = "INSERT INTO users (fullname, username, email, school) VALUES ('$fullname', '$username', '$email', '$school')";
        if (mysqli_query($connect, $sql)) {
            return true;
        } else {
            return false;
        }
    }
    

    function getSchool($connection, $username)
    {
        $query = "SELECT school FROM users WHERE username = '$username'";
        $result = mysqli_query($connection, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return $row['school'];
        } else {
            // Handle error if query fails
            return false;
        }
    }

    function getSchoolInfo($connection, $school) {
        $query = "SELECT * FROM " . $school;
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            // Store values in variables
            $primaryColor = $row['primaryColor'];
            $secondaryColor = $row['secondaryColor'];
            $primaryModifiedColor = $row['primaryModifiedColor'];
            $topItem = $row['topItem'];
            $upcomingEvent1 = $row['upcomingEvent1'];
            $upcomingEvent2 = $row['upcomingEvent2'];
            $upcomingEvent3 = $row['upcomingEvent3'];
            $topTenTips = $row['topTenTips'];
            $upcomingEventsImage = $row['upcomingEventsImage'];
            $logo = $row['logo'];
            $waves = $row['waves'];
            $studentLogo = $row['studentLogo'];
            $prizesImage = $row['prizesImage'];
            $uploadImage = $row['uploadImage'];
            $leaderboardImage = $row['leaderboardImage'];
            // Return an array of values
            return array(
                'primaryColor' => $primaryColor,
                'secondaryColor' => $secondaryColor,
                'primaryModifiedColor' => $primaryModifiedColor,
                'topItem' => $topItem,
                'upcomingEvent1' => $upcomingEvent1,
                'upcomingEvent2' => $upcomingEvent2,
                'upcomingEvent3' => $upcomingEvent3,
                'topTenTips' => $topTenTips,
                'upcomingEventsImage' => $upcomingEventsImage,
                'logo' => $logo,
                'waves' => $waves,
                'studentLogo' => $studentLogo,
                'prizesImage' => $prizesImage,
                'uploadImage' => $uploadImage,
                'leaderboardImage' => $leaderboardImage
            );
        } else {
            // Handle error if query fails or no rows found
            return false;
        }
     }

    // New function to retrieve user details
    function getUserDetails($table, $username)
    {
        $username = $this->prepareData($username);
        $this->sql = "SELECT fullname, fname, totalPoints FROM $table WHERE username = '$username'";  //place to grab any information neccessary for next slides
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row;
        }

        return null;
    }

    function updatePassword($username, $newPassword, $school)
    {
        $username = $this->prepareData($username);
        $newPassword = $this->prepareData($newPassword);
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        //Chnage to $schoolusers table
        $this->sql = "UPDATE " . $school . "users SET password = '$newPassword' WHERE username = '$username'";
        return mysqli_query($this->connect, $this->sql);
    }

    function usernameExists($username)
    {
        $username = $this->prepareData($username);
        //Chnage to $schoolusers table
        $this->sql = "SELECT username FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($this->connect, $this->sql);
        
        return mysqli_num_rows($result) > 0;
    }














    function generateVerificationCode()
    {
        // Generate a random 4-digit verification code
        return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    function getEmailByUsername($username)
    {
        $username = $this->prepareData($username);
        //Chnage to $schoolusers table
        $this->sql = "SELECT email FROM users WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['email'];
        }

        return null;
    }

    function getPhoneNumberByUsername($username)
    {
        $username = $this->prepareData($username);
        //Chnage to $schoolusers table
        $this->sql = "SELECT phoneNumber FROM users WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['phoneNumber'];
        }

        return null;
    }

    function storeVerificationCode($username, $verificationCode, $school)
    {
        $username = $this->prepareData($username);
        $verificationCode = $this->prepareData($verificationCode);
        //Chnage to $schoolusers table
        $this->sql = "UPDATE " . $school . "users SET verificationCode = '$verificationCode' WHERE username = '$username'";
        return mysqli_query($this->connect, $this->sql);
    }

    function checkVerificationCode($username, $enteredCode)
    {
        /*
        $username = $this->prepareData($username);
        $enteredCode = $this->prepareData($enteredCode);*/

        // Retrieve the verification code from the database

        //Chnage to $schoolusers table
        $this->sql = "SELECT verificationCode FROM users WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $storedCode = $row['verificationCode'];

            // Compare the entered code with the stored code
            return ($enteredCode == $storedCode);
        }

        return false;
    }



    function grabUsernameByEmail($email)
    {
        //Chnage to $schoolusers table
        $this->sql = "SELECT username FROM users WHERE email = '$email'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $username = $row['username'];

            // Compare the entered code with the stored code
            return $username;
        }

        return false;
    }






    function generateAndStoreVerificationCode($username)
    {
        $verificationCode = $this->generateVerificationCode();
        $this->storeVerificationCode($username, $verificationCode);
    
        return $verificationCode;
    }






    //App Password: qmzv cmth wmzf ivmy
    function sendVerificationCodeByEmail($email, $verificationCode)
    {
      $mail = new PHPMailer(true);

       try {
           //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dflphp@gmail.com';  // Replace with your Gmail address
            $mail->Password   = 'qmzv cmth wmzf ivmy';    // Replace with your App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('dflphp@gmail.com', 'Fan League');  // Replace with your name
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Verification Code';
            $mail->Body    = "Your verification code is: $verificationCode";

            // Send email
            $mail->send();

            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            // Log or handle the error
            return false;
        }
    }
    



    function infoEmail($email, $username, $event, $subDate)
    {
      $mail = new PHPMailer(true);

       try {
           //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dflphp@gmail.com';  // Replace with your Gmail address
            $mail->Password   = 'qmzv cmth wmzf ivmy';    // Replace with your App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('dflphp@gmail.com', 'Fan League');  // Replace with your name
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'We need more Info';
            $mail->Body    = "Hello $username,<br>we need some more information about your recent submission for $event on $subDate<br><br>Please Respond ASAP...";

            // Send email
            $mail->send();

            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            // Log or handle the error
            return false;
        }
    }

    function rejectEmail($email, $username, $event, $subDate)
    {
      $mail = new PHPMailer(true);

       try {
           //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dflphp@gmail.com';  // Replace with your Gmail address
            $mail->Password   = 'qmzv cmth wmzf ivmy';    // Replace with your App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('dflphp@gmail.com', 'Fan League');  // Replace with your name
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'I hope this email finds you before I do';
            $mail->Body    = "Hello $username,<br>we have reason to believe your recent submission for $event on $subDate is inauthentic putting you under investigation for cheating.<br><br>Let's talk...";

            // Send email
            $mail->send();

            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            // Log or handle the error
            return false;
        }
    }

    
    
    function sendVerificationCodeBySMS($phoneNumber, $verificationCode) {
        $sid = 'ACb5ab836b72f223d088f4cc29322336d3';
        $token = '24d7f89b986dbbf6efb4d3d978f75b31';
        $twilioNumber = '+18664052671';
    
        $client = new Client($sid, $token);
    
        try {
            $message = $client->messages->create(
                $phoneNumber, // Destination phone number
                array(
                    'from' => $twilioNumber,
                    'body' => "Your verification code is: $verificationCode"
                )
            );
    
            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            // Log or handle the error
            return false;
        }
    }



    function getSchoolName($username)
    {
        //Chnage to $schoolusers table
        $this->sql = "SELECT school FROM users WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $school = $row['school'];

            // Compare the entered code with the stored code
            return $school;
        }

        return false;
    }

    function getContactInfoByUsername($username, $school)
    {
        $username = $this->prepareData($username);
        //Chnage to $schoolusers table

        $table = $school . "users"; // Ensure $table is constructed properly
        $this->sql = "SELECT phoneNumber, email FROM " . $table . " WHERE username = '$username'";
        $result = mysqli_query($this->connect, $this->sql);

        if (!$result) {
            // If there's an error with the SQL query, log the error and return null
            error_log("SQL Error: " . mysqli_error($this->connect));
            return null;
        }

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return [
                'phoneNumber' => $row['phoneNumber'],
                'email' => $row['email'],
            ];
        }

        else {
            // If no rows are returned, log a message and return null
            error_log("No contact info found for username: $username and school: $school");
            return null;
        }
    }

    function eventPoints($event)
    {
        if ($event === "basketball") {
            return 1;
        } elseif ($event === "football") {
            return 1;
        } elseif ($event === "baseball_softball") {
            return 1;
        } elseif ($event === "soccer") {
            return 2;
        } elseif ($event === "lacrosse") {
            return 2;
        } elseif ($event === "volleyball") {
            return 4;
        } elseif ($event === "tennis") {
            return 4;
        } elseif ($event === "wrestling") {
            return 6;
        } elseif ($event === "fencing") {
            return 10;
        } elseif ($event === "track_field") {
            return 10;
        } elseif ($event === "cross_country") {
            return 10;
        } elseif ($event === "swimming_diving") {
            return 12;
        } elseif ($event === "golf") {
            return 12;
        } elseif ($event === "field_hockey") {
            return 14;
        } elseif ($event === "womens_rowing") {
            return 14;
        } else {
            return 0; // Return 0 points for other events or invalid options
        }
    }

}
?>
