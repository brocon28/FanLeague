<?php
session_start(); // Start the session

if (isset($_SESSION['username'])) {
    $fullname = $_SESSION['fullname'];
    $username = $_SESSION['username'];
    $fname = $_SESSION['fname'];
} else {
    echo "User not logged in.";
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}
require "DataBase.php";

// Create an instance of the Database class
$database = new DataBase();

// Connect to the database
$connection = $database->dbConnect();

if (strlen($fullname) > 15) {
   // Split the fullname into an array of words
   $nameParts = explode(' ', $fullname);

   // Take the first part of the name
   $firstName = $nameParts[0];

   // Take the first letter of the last name
   $lastNameInitial = substr($nameParts[count($nameParts) - 1], 0, 1);

   // Concatenate the first name and the first initial of the last name
   $shortenedName = $firstName . " " . $lastNameInitial . ".";

   // Use $shortenedName as needed
   $fullname = $shortenedName;
} 
global $school;
$school = $database->getSchool($connection, $username);
$schoolInfo = $database->getSchoolInfo($connection, $school);
$topItem = $schoolInfo['topItem'];
global $topItem;
// Check if data was retrieved successfully
if ($schoolInfo !== false) {
   // Access individual variables
   $primaryColor = $schoolInfo['primaryColor'];
   $secondaryColor = $schoolInfo['secondaryColor'];
   $primaryModifiedColor = $schoolInfo['primaryModifiedColor'];
   $topItem = $schoolInfo['topItem'];
   $upcomingEvent1 = $schoolInfo['upcomingEvent1'];
   $upcomingEvent2 = $schoolInfo['upcomingEvent2'];
   $upcomingEvent3 = $schoolInfo['upcomingEvent3'];
   $topTenTips = $schoolInfo['topTenTips'];
   $upcomingEventsImage = $schoolInfo['upcomingEventsImage'];
   $logo = $schoolInfo['logo'];
   $waves = $schoolInfo['waves'];
   $studentLogo = $schoolInfo['studentLogo'];
   $prizesImage = $schoolInfo['prizesImage'];
   $uploadImage = $schoolInfo['uploadImage'];
   $leaderboardImage = $schoolInfo['leaderboardImage'];
   
} else {
   // Handle the case where data retrieval failed
   echo "Failed to retrieve school information.";
}


// Check if the connection was successful
if (!$connection) {
    die("ERROR: Could not connect to the database. " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Retrieve form data
   $photo = $_FILES['photo-upload']; // Get the uploaded file data
   $event = $_POST['sport'];
   $seatNum = $_POST['seat-number'];
   $gameCode = $_POST['code'];
   $username = $_SESSION['username']; // Get the username from session
   //$points = eventPoints($event);
   
   // Get contact information (email and phone number) by username
   $contactInfo = $database->getContactInfoByUsername($username, $school);
   $email = $contactInfo['email']; // Retrieve email from contact information
   $phoneNum = $contactInfo['phoneNumber']; // Retrieve phone number from contact information

   // Check if file was uploaded successfully
   //$points = eventPoints($event);
   $points = $database->eventPoints($event);
   $date = date("Y-m-d");
   




   if ($photo['error'] === UPLOAD_ERR_OK) {
       // Get the temporary file name
       $tmpFilePath = $photo['tmp_name'];

       // Read the contents of the file into a variable
       $photoData = file_get_contents($tmpFilePath);

       // Escape special characters in the binary data
       $escapedPhotoData = mysqli_real_escape_string($connection, $photoData);

       // Insert the photo data, username, email, and phone number into the tickets table
       $query = "INSERT INTO tickets (photo, username, school, event, seatNum, gameCode, email, phoneNum, points, subDate) VALUES ('$escapedPhotoData', '$username', '$school', '$event', '$seatNum', '$gameCode', '$email', '$phoneNum', '$points', '$date')";

       // Execute the query
       $result = mysqli_query($connection, $query);

       if ($result) {
           // Data successfully inserted into the tickets table
           //echo "Ticket information uploaded successfully.";
           //echo $event;
           header("Location: home.php");
       } else {
           // Error inserting data into the tickets table
           echo "Error uploading ticket information.";
           echo "Error: " . mysqli_error($connection);
       }
   } else {
       // File upload error
       echo "Error: " . $photo['error'];
   }

   // You can now use $event, $seatNum, $gameCode, $email, and $phoneNum as needed
   //echo "$photo, $event, $seatNum, $gameCode, $email, $phoneNum";
}
// Close the database connection
mysqli_close($connection);
?>


<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Upload Points</title>
      <link rel="stylesheet" href="upload-styles.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Asap+Condensed:wght@400;700&family=Barlow:wght@400;700&display=swap" rel="stylesheet">
      <style>
         .sidebar {
            background: linear-gradient(to bottom, <?php echo $primaryColor; ?>, <?php echo $secondaryColor; ?>);
         }
         .upload-container h2 {
            color: <?php echo $primaryColor; ?>;
         }
         .form-group input[type="text"]:onclick, .form-group input[type="email"]:onclick, .form-group input[type="tel"]:onclick, .form-group input[type="file"]:onclick, .form-group input[type="submit"]:onclick {
            border: 2px solid <?php echo $primaryColor; ?>;
         }
         .form-group:not(.file-upload-group) input:focus+label, .form-group:not(.file-upload-group) input:not(:placeholder-shown)+label {
            color: <?php echo $primaryColor; ?>;
         }
         .form-group input:focus, .form-group input:not(:placeholder-shown) {
            border-color: <?php echo $primaryColor; ?>;
         }
         .form-group input[type="submit"] {
            background-color: <?php echo $primaryColor; ?>;
         }
         .form-group input[type="submit"]:hover {
            background-color: <?php echo $secondaryColor; ?>;
         }
         .form-group input[type="file"]:focus+label, .form-group input[type="submit"]:hover {
            border-color: <?php echo $primaryColor; ?>;
         }
         .file-upload-label.active {
            color: <?php echo $primaryColor; ?>;
         }
         .sport-form-group label.active {
            color: <?php echo $primaryColor; ?>;
         }
         .sport-form-group select.active {
            border: 1px solid <?php echo $primaryColor; ?>; /* Blue border color */
         }
         .file-upload-group.active {
            border: 1px solid <?php echo $primaryColor; ?>;
         }

         <?php
         // Check if $studentLogo is set and not empty
         if (isset($uploadImage) && !empty($uploadImage)) {
            // If image data is available, echo the CSS rule with base64 encoded image data
            echo '.blurred-background {
                        background-image: url("data:image/png;base64,' . base64_encode($uploadImage) . '");
                     }';
         } else {
            // If no image data is available, use the default image
            echo '.blurred-background {
                        background-image: url("pictures/userLogo.png");
                     }';
         }
         ?>

      </style>
   </head>
   <body>
      <!--background image-->
      <div class="blurred-background"></div>
      <div class="container">
         <!--sidebar-->
         <div class="sidebar">
            <div class="sidebar-top">
               <h1 class="league-title"><?php echo $school; ?><br/>Fan League</h1>
               <div class="profile-section">
                  <?php
                  // Check if $logoData is set and not empty
                  if (isset($studentLogo) && !empty($studentLogo)) {
                     // If image data is available, echo the image tag with base64 encoded image data
                     echo '<img src="data:image/png;base64,' . base64_encode($studentLogo) . '" alt="User Image" class="profile-pic" />';
                  } else {
                     // If no image data is available, display a placeholder image
                     echo '<img src="pictures/userLogo.png" alt="User Image" class="profile-pic" />';
                  }
                  ?>
                  <!--<img src="pictures/userLogo.png" alt="User Image" class="profile-pic" />-->
                  <div class="user-info">
                     <div class="user-name"><?php echo isset($fullname) ? $fullname : ''; ?></div>
                     
                     <!--<div class="user-name">Sean Forbes</div>-->
                     <div class="user-role">Student</div>
                  </div>
               </div>
               <div class="nav">
                  <div class="nav-item"><a href="home.php">Dashboard</a></div>
                  <div class="nav-item"><a href="leaderboard.php">Leaderboard</a></div>
                  <div class="nav-item"><a href="prizes.php">Prizes</a></div>
                  <div class="nav-item"><a href="howToPlay.php">How to play</a></div>
                  <div class="nav-last">
                     <a href="login.php" class="sign-out-link">Sign Out</a>
                  </div>
               </div>
            </div>
         </div>
         <div class="main-content">
            <!--start of upload container-->
            <div class="upload-container">
               <div class="upload-header">
                  <h2>New Points Upload</h2>
                  <p>We will send submission confirmations and point updates</p>
               </div>
               <!--once form is complete return back to home.html-->
               <form class="upload-form" action="upload.php" method="post" enctype="multipart/form-data">
                  <div class="form-group file-upload-group">
                     <input id="photo-upload" type="file" name="photo-upload" required hidden onchange="handleFileUpload(this)" />
                     <label for="photo-upload" class="file-upload-label">Photo Upload</label>
                     <span id="file-name" class="file-name"></span>
                     <div class="file-upload-btn-wrapper">
                        <button type="button" class="file-upload-btn" onclick="document.getElementById('photo-upload').click();">
                        <img src="pictures/upload.png" alt="Upload" />
                        </button>
                     </div>
                  </div>
                  <div class="form-group sport-form-group">
                     <select id="sport" name="sport" required>
                        <option value="" disabled selected></option>
                        <option value="basketball">Basketball</option>
                        <option value="football">Football</option>
                        <option value="baseball_softball">Baseball/Softball</option>
                        <option value="soccer">Soccer</option>
                        <option value="lacrosse">Lacrosse</option>
                        <option value="volleyball">Volleyball</option>
                        <option value="tennis">Tennis</option>
                        <option value="wrestling">Wrestling</option>
                        <option value="fencing">Fencing</option>
                        <option value="track_field">Track and Field</option>
                        <option value="cross_country">Cross Country</option>
                        <option value="swimming_diving">Swimming and Diving</option>
                        <option value="golf">Golf</option>
                        <option value="field_hockey">Field Hockey</option>
                        <option value="womens_rowing">Women's Rowing</option>
                        <!-- Add more options as needed -->
                     </select>
                     <label class="sport-label" for="sport">Sport/Event</label>
                  </div>
                  <!--<div class="form-group">
                     <input id="sport" type="text" name="sport" placeholder=" " required/>
                     <label for="sport">Sport/Event</label>
                  </div>-->
                  <div class="form-group">
                     <input id="seat-number" type="text" name="seat-number" placeholder=" " required/>
                     <label for="seat-number">Seat Number</label>
                  </div>
                  <div class="form-group">
                     <!--<input id="phone-number" type="tel" name="phone-number" placeholder=" " pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required/>
                     <label for="phone-number">Phone Number</label>-->
                     <input id="code" type="text" name="code" placeholder=" " required/>
                     <label for="code">Game Code</label>
                  </div>
                  <div class="form-group">
                     <input type="submit" value="Submit"/>
                  </div>
               </form>
            </div>
         </div>
      </div>
      <!--This script allows the user to pull a photo from their file explorer and upload it-->
      <script src="https://kit.fontawesome.com/a076d05399.js"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script>
         function handleFileUpload(input) {
             var fileName = input.files[0].name;
             document.getElementById('file-name').textContent = fileName; // Display file name
         
             var label = document.querySelector('.file-upload-label');
             label.classList.add('active'); // Add 'active' class to label
         
             var container = document.querySelector('.file-upload-group');
             container.classList.add('active'); // Add 'active' class to container
         }

         
         $(document).ready(function() {
            // Function to handle changes in the dropdown
            $('#sport').on('change', function() {
                  var selectedValue = $(this).val();
                  var label = $('.sport-form-group label');
                  // Check if a value is selected
                  if (selectedValue !== '') {
                     label.addClass('active').css('color', '<?php echo $primaryColor; ?>');
                  } else {
                     label.removeClass('active').css('color', '#999');
                  }
            });


            $(document).ready(function() {
               // Function to handle changes in the dropdown
               $('#sport').on('change', function() {
                  var selectedValue = $(this).val();
                  var selectElement = $('.sport-form-group select');

                  // Check if a value is selected
                  if (selectedValue !== '') {
                        selectElement.addClass('active');
                  } else {
                        selectElement.removeClass('active');
                  }
               });
            });


            // Trigger the change event initially to ensure proper styling on page load
            //$('#sport').trigger('change');
         });
      </script>
   </body>
</html>