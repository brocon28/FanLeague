<?php
// Enable error reporting to display any errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start the session
require "DataBase.php";

// Function to establish a database connection
function connectToDatabase() {
    $database = new DataBase();
    $connection = $database->dbConnect();
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $connection;
}

// PHP code to handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form submitted<br>"; // Debugging message to verify form submission

    // Retrieve form data
    $schoolName = $_POST['schoolName'];
    $primaryColor = $_POST['primaryColor'];
    $secondaryColor = $_POST['secondaryColor'];
    $primaryModifiedColor = $_POST['primaryModifiedColor'];
    $topItem = $_POST['topItem'];
    $upcomingEvent1 = $_POST['upcomingEvent1'];
    $upcomingEvent2 = $_POST['upcomingEvent2'];
    $upcomingEvent3 = $_POST['upcomingEvent3'];
    $topTenTips = $_POST['topTenTips'];

    $connection = connectToDatabase(); // Establish connection


    // Create table if not exists
    $tableName = $schoolName;
    $createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        primaryColor VARCHAR(50),
        secondaryColor VARCHAR(50),
        primaryModifiedColor VARCHAR(50),
        topItem VARCHAR(50),
        upcomingEvent1 VARCHAR(300),
        upcomingEvent2 VARCHAR(300),
        upcomingEvent3 VARCHAR(300),
        topTenTips VARCHAR(300),
        upcomingEventsImage LONGBLOB,
        logo LONGBLOB,
        waves LONGBLOB,
        studentLogo LONGBLOB,
        prizesImage LONGBLOB,
        uploadImage LONGBLOB,
        leaderboardImage LONGBLOB
    )";
    if (!mysqli_query($connection, $createTableQuery)) {
        echo "Error creating table: " . mysqli_error($connection);
        mysqli_close($connection);
        exit; // Exit script if table creation fails
    }


    /*$insertQuery2 = "INSERT INTO schools (schoolName) VALUES ('$schoolName')";

    // Display insert query
    echo "Insert query: $insertQuery2<br>";

    // Attempt to insert data
    if (!mysqli_query($connection, $insertQuery2)) {
        echo "Error inserting data: " . mysqli_error($connection);
        mysqli_close($connection);
        exit;
    }*/

    // Define the table name for users
    $usersTableName = $tableName . "users";

    // Create table query for users with specified adjustments
    $createUsersTableQuery = "CREATE TABLE IF NOT EXISTS $usersTableName (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100) NOT NULL,
        username VARCHAR(50) NOT NULL,
        password VARCHAR(150) NOT NULL,
        email VARCHAR(100) NOT NULL,
        totalPoints INT(3) DEFAULT 0,
        fname VARCHAR(50) NOT NULL,
        userRank INT(11) DEFAULT NULL,
        recentPoints INT(11) NOT NULL DEFAULT 0,
        phoneNumber VARCHAR(16) NOT NULL DEFAULT 'xxx-xxx-xxxx',
        verificationCode VARCHAR(4) DEFAULT NULL,
        school VARCHAR(30) NOT NULL
    )";

    // Execute the query to create the users table
    if (!mysqli_query($connection, $createUsersTableQuery)) {
        echo "Error creating users table: " . mysqli_error($connection);
        mysqli_close($connection);
        exit; // Exit script if table creation fails
    }


    // Handle file uploads
    function handleFileUpload($fieldName, $connection) {
        $fileName = $_FILES[$fieldName]['name'];
        $fileTmpName = $_FILES[$fieldName]['tmp_name'];
        $fileSize = $_FILES[$fieldName]['size'];
        $fileError = $_FILES[$fieldName]['error'];
        $fileType = $_FILES[$fieldName]['type'];

        // Check if file was uploaded successfully
        if ($fileError === UPLOAD_ERR_OK) {
            // Read the contents of the file into a variable
            $fileData = file_get_contents($fileTmpName);

            // Escape special characters in the binary data
            $escapedFileData = mysqli_real_escape_string($connection, $fileData);

            return $escapedFileData;
        } else {
            return null; // Return null if file upload failed
        }
    }

    // Insert form data into the table
    $upcomingEventsImageData = handleFileUpload('upcomingEventsImage', $connection);
    $logoData = handleFileUpload('logo', $connection);
    $wavesData = handleFileUpload('waves', $connection);
    $studentLogoData = handleFileUpload('studentLogo', $connection);
    $prizesImageData = handleFileUpload('prizesImage', $connection);
    $uploadImageData = handleFileUpload('uploadImage', $connection);
    $leaderboardImageData = handleFileUpload('leaderboardImage', $connection);

    // Debugging: Output the SQL query to check its correctness
    echo "Inserting data...<br>";

    $insertQuery = "INSERT INTO $tableName (primaryColor, secondaryColor, primaryModifiedColor, topItem, upcomingEvent1, upcomingEvent2, upcomingEvent3, topTenTips)
                    VALUES ('$primaryColor', '$secondaryColor', '$primaryModifiedColor', '$topItem', '$upcomingEvent1', '$upcomingEvent2', '$upcomingEvent3', '$topTenTips')";
    echo "Insert query: $insertQuery<br>";


    // Attempt to insert non-image data
    if (!mysqli_query($connection, $insertQuery)) {
        echo "Error inserting data: " . mysqli_error($connection);
        mysqli_close($connection);
        exit;
    }


    // Close connection before inserting images
    mysqli_close($connection);

    // Insert each image into its respective column with individual UPDATE queries

    // Insert Upcoming Events Image
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertUpcomingEventsImageQuery = "UPDATE $tableName SET upcomingEventsImage = '$upcomingEventsImageData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertUpcomingEventsImageQuery)) {
            echo "Error inserting upcoming events image (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert upcoming events image.<br>";
            }
        } else {
            echo "Upcoming events image inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Insert Logo
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertLogoQuery = "UPDATE $tableName SET logo = '$logoData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertLogoQuery)) {
            echo "Error inserting logo (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert logo.<br>";
            }
        } else {
            echo "Logo inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Insert Waves Image
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertWavesQuery = "UPDATE $tableName SET waves = '$wavesData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertWavesQuery)) {
            echo "Error inserting waves image (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert waves image.<br>";
            }
        } else {
            echo "Waves image inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Insert Student Logo Image (Repeat this process for other images)
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertStudentLogoQuery = "UPDATE $tableName SET studentLogo = '$studentLogoData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertStudentLogoQuery)) {
            echo "Error inserting student logo image (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert student logo image.<br>";
            }
        } else {
            echo "Student logo image inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Insert Prizes Image
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertPrizesImageQuery = "UPDATE $tableName SET prizesImage = '$prizesImageData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertPrizesImageQuery)) {
            echo "Error inserting prizes image (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert prizes image.<br>";
            }
        } else {
            echo "Prizes image inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Insert Upload Image
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertUploadImageQuery = "UPDATE $tableName SET uploadImage = '$uploadImageData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertUploadImageQuery)) {
            echo "Error inserting upload image (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert upload image.<br>";
            }
        } else {
            echo "Upload image inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Insert Leaderboard Image
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        $connection = connectToDatabase(); // Open connection
        $insertLeaderboardImageQuery = "UPDATE $tableName SET leaderboardImage = '$leaderboardImageData' WHERE primaryColor = '$primaryColor'";
        if (!mysqli_query($connection, $insertLeaderboardImageQuery)) {
            echo "Error inserting leaderboard image (attempt $attempt): " . mysqli_error($connection) . "<br>";
            if ($attempt === 3) {
                echo "Max attempts reached. Unable to insert leaderboard image.<br>";
            }
        } else {
            echo "Leaderboard image inserted successfully.<br>";
            break;
        }
        mysqli_close($connection); // Close connection
    }

    // Close connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
</head>
<body>
    <h2>Form</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="schoolName">School Name:</label>
        <input type="text" id="schoolName" name="schoolName" required><br><br>

        <label for="primaryColor">Primary Color:</label>
        <input type="text" id="primaryColor" name="primaryColor" required><br><br>

        <label for="secondaryColor">Secondary Color:</label>
        <input type="text" id="secondaryColor" name="secondaryColor" required><br><br>

        <label for="primaryModifiedColor">Primary Modified Color:</label>
        <input type="text" id="primaryModifiedColor" name="primaryModifiedColor" required><br><br>

        <label for="topItem">Top Item:</label>
        <input type="text" id="topItem" name="topItem" required><br><br>

        <label for="upcomingEvent1">Upcoming Event 1:</label>
        <input type="text" id="upcomingEvent1" name="upcomingEvent1" required><br><br>

        <label for="upcomingEvent2">Upcoming Event 2:</label>
        <input type="text" id="upcomingEvent2" name="upcomingEvent2" required><br><br>

        <label for="upcomingEvent3">Upcoming Event 3:</label>
        <input type="text" id="upcomingEvent3" name="upcomingEvent3" required><br><br>

        <label for="topTenTips">Top Ten Tips:</label>
        <input type="text" id="topTenTips" name="topTenTips" required><br><br>

        <label for="upcomingEventsImage">Upcoming Events Image:</label>
        <input type="file" id="upcomingEventsImage" name="upcomingEventsImage" required><br><br>

        <label for="logo">Logo:</label>
        <input type="file" id="logo" name="logo" required><br><br>

        <label for="waves">Waves:</label>
        <input type="file" id="waves" name="waves" required><br><br>

        <label for="studentLogo">Student Logo:</label>
        <input type="file" id="studentLogo" name="studentLogo" required><br><br>

        <label for="prizesImage">Prizes Image:</label>
        <input type="file" id="prizesImage" name="prizesImage" required><br><br>

        <label for="uploadImage">Upload Image:</label>
        <input type="file" id="uploadImage" name="uploadImage" required><br><br>

        <label for="leaderboardImage">Leaderboard Image:</label>
        <input type="file" id="leaderboardImage" name="leaderboardImage" required><br><br>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
