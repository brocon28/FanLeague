<?php
// Start the session and include necessary files
if (session_status() == PHP_SESSION_NONE) {
    // Start the session if it hasn't been started already
    session_start();
}
//require "DataBase.php";
include "DataBase.php";

// Create an instance of the Database class
$database = new DataBase();

// Connect to the database
$connection = $database->dbConnect();

// Retrieve tickets information from the database
$query = "SELECT * FROM tickets";
$result = mysqli_query($connection, $query);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id=0;
    // Extract ID from the value of the button
    if (strpos($action, 'approve') === 0) {
        $id = substr($action, 7);
    } elseif (strpos($action, 'info') === 0) {
        $id = substr($action, 4);
    } elseif (strpos($action, 'reject') === 0) {
        $id = substr($action, 6);
    }
    
    // Check if the action is "approve" and if ID exists in POST data
    if (strpos($action, 'approve') === 0 && isset($_POST['username' . $id])) {
        $username = $_POST['username' . $id];
        $username = preg_replace('/' . $id . '$/', '', $username);
        $school = $_POST['school' . $id];
        $school = preg_replace('/' . $id . '$/', '', $school);
        // Retrieve other data associated with the same ID
        $email = $_POST['email' . $id];
        $phoneNum = $_POST['phoneNum' . $id];
        $points = $_POST['points' . $id];
        $points = preg_replace('/' . $id . '$/', '', $points);
        $gameCode = $_POST['gameCode' . $id];
        $event = $_POST['event' . $id];
        $seatNum = $_POST['seatNum' . $id];
        $subDate = $_POST['subDate' . $id];
        $subDate = preg_replace('/' . $id . '$/', '', $subDate);

        $table = $school . "users";
        
        // Now you have all the data associated with the selected row
        // You can perform further processing here
        echo "Hooray! Approve button selected for user $username with $points points.";
        $updateQuery = "UPDATE " . $table . " SET totalPoints = totalPoints + $points WHERE username = '$username'";
        if(mysqli_query($connection, $updateQuery)) {
            echo "Points updated successfully!";
            
            // Delete the row from the tickets table
            $deleteQuery = "DELETE FROM tickets WHERE username = '$username' AND points = '$points' AND subDate = '$subDate'";
            if(mysqli_query($connection, $deleteQuery)) {
                echo "Ticket deleted successfully!";
                header("Location: verification.php");
                exit();
            } else {
                echo "Error deleting ticket: " . mysqli_error($connection);
            }
        } else {
            echo "Error updating points: " . mysqli_error($connection);
        }

        
    }



    if (strpos($action, 'info') === 0 && isset($_POST['username' . $id])) {
        $username = $_POST['username' . $id];
        $username = preg_replace('/' . $id . '$/', '', $username);
        // Retrieve other data associated with the same ID
        $email = $_POST['email' . $id];
        $email = preg_replace('/' . $id . '$/', '', $email);
        $phoneNum = $_POST['phoneNum' . $id];
        $points = $_POST['points' . $id];
        $points = preg_replace('/' . $id . '$/', '', $points);
        $gameCode = $_POST['gameCode' . $id];
        $event = $_POST['event' . $id];
        $event = preg_replace('/' . $id . '$/', '', $event);
        $seatNum = $_POST['seatNum' . $id];
        $subDate = $_POST['subDate' . $id];
        $subDate = preg_replace('/' . $id . '$/', '', $subDate);
        
        // Now you have all the data associated with the selected row
        // You can perform further processing here
        // Path to the info.png image
        // Path to the picture file
        $picturePath = "pictures/info.png";

        // Read the contents of the picture file into a variable
        $pictureData = file_get_contents($picturePath);

        // Escape special characters in the binary data
        $escapedPictureData = mysqli_real_escape_string($connection, $pictureData);

        // Update the "flag" column in the tickets table with the escaped picture data
        $updateQuery = "UPDATE tickets SET flag = '$escapedPictureData' WHERE username = '$username' AND points = '$points' AND subDate = '$subDate'";
        if(mysqli_query($connection, $updateQuery)) {
            $success = $database->infoEmail($email, $username, $event, $subDate);
            header("Location: verification.php");
            exit();
            
        } else {
            echo "Error updating picture: " . mysqli_error($connection);
        }
    } 

    if (strpos($action, 'reject') === 0 && isset($_POST['username' . $id])) {
        $username = $_POST['username' . $id];
        $username = preg_replace('/' . $id . '$/', '', $username);
        // Retrieve other data associated with the same ID
        $email = $_POST['email' . $id];
        $email = preg_replace('/' . $id . '$/', '', $email);
        $phoneNum = $_POST['phoneNum' . $id];
        $points = $_POST['points' . $id];
        $points = preg_replace('/' . $id . '$/', '', $points);
        $gameCode = $_POST['gameCode' . $id];
        $event = $_POST['event' . $id];
        $event = preg_replace('/' . $id . '$/', '', $event);
        $seatNum = $_POST['seatNum' . $id];
        $subDate = $_POST['subDate' . $id];
        $subDate = preg_replace('/' . $id . '$/', '', $subDate);
        
        // Now you have all the data associated with the selected row
        // You can perform further processing here
        // Path to the info.png image
        // Path to the picture file
        $picturePath = "pictures/reject.png";

        // Read the contents of the picture file into a variable
        $pictureData = file_get_contents($picturePath);

        // Escape special characters in the binary data
        $escapedPictureData = mysqli_real_escape_string($connection, $pictureData);

        // Update the "flag" column in the tickets table with the escaped picture data
        $updateQuery = "UPDATE tickets SET flag = '$escapedPictureData' WHERE username = '$username' AND points = '$points' AND subDate = '$subDate'";
        if(mysqli_query($connection, $updateQuery)) {
            $success = $database->rejectEmail($email, $username, $event, $subDate);
            header("Location: verification.php");
            exit();
        } else {
            echo "Error updating picture: " . mysqli_error($connection);
        }
    } 

}

// Define a function to generate the table HTML
function generateTableHTML($result) {
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<form method='POST' action='verification.php'>";
        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Flag</th>
                <th>Username</th>
                <th>School</th>
                <th>Email</th>
                <th>Phone#</th>
                <th>Points</th>
                <th>Game Code</th>
                <th>Event</th>
                <th>Date</th>
                <th>Seat#</th>
                <th>Photo</th>
                <th>Action</th>
              </tr>";
        $id = 1; // Initialize ID counter
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $id . "</td>"; // Output the ID for each row
            echo "<td>";
            if ($row['flag'] !== NULL) {
                echo '<img class="ticket-photo" src="data:image/png;base64,' . base64_encode($row['flag']) . '" alt="Ticket Photo">';
            }
            else{
                echo '';
            }
            echo "</td>";

            //echo '<td><img class="ticket-photo" src="data:image/png;base64,' . base64_encode($row['flag']) . '" alt="Ticket Photo"></td>';
            echo "<td><input type='hidden' name='username$id' value='" . $row['username'] . $id . "'>" . $row['username'] . "</td>";
            echo "<td><input type='hidden' name='school$id' value='" . $row['school'] . $id . "'>" . $row['school'] . "</td>";
            echo "<td><input type='hidden' name='email$id' value='" . $row['email'] . $id . "'>" . $row['email'] . "</td>";
            echo "<td><input type='hidden' name='phoneNum$id' value='" . $row['phoneNum'] . $id . "'>" . $row['phoneNum'] . "</td>";
            echo "<td><input type='hidden' name='points$id' value='" . $row['points'] . $id . "'>" . $row['points'] . "</td>";
            echo "<td><input type='hidden' name='gameCode$id' value='" . $row['gameCode'] . $id . "'>" . $row['gameCode'] . "</td>";
            echo "<td><input type='hidden' name='event$id' value='" . $row['event'] . $id . "'>" . $row['event'] . "</td>";
            echo "<td><input type='hidden' name='subDate$id' value='" . $row['subDate'] . $id . "'>" . $row['subDate'] . "</td>";
            echo "<td><input type='hidden' name='seatNum$id' value='" . $row['seatNum'] . $id . "'>" . $row['seatNum'] . "</td>";
            echo '<td><img class="ticket-photo" src="data:image/png;base64,' . base64_encode($row['photo']) . '" alt="Ticket Photo"></td>';
            echo '<td>
                    <div class="button-container">
                        <button class="approve-button" type="submit" name="action" value="approve' . $id . '"></button>
                        <button class="info-button" type="submit" name="action" value="info' . $id . '"></button>
                        <button class="reject-button" type="submit" name="action" value="reject' . $id . '"></button>
                    </div>
                  </td>';
            echo "</tr>";
            $id++; // Increment ID counter
        }
        echo "</table>";
        echo "</form>";
    } else {
        // No tickets found
        echo "No tickets found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Verification</title>
    <link rel="stylesheet" href="verification.css">
</head>
<body>
    <div class="ticket-verification">
        <h1>Ticket Verification</h1>
        <div class="tickets-table">
            <?php generateTableHTML($result); ?>
        </div>
        <a href="vlogin.php" class="back-to-login"><img src="pictures\signOut.png" alt="Back to Login"></a>
    </div>
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
    </div>
    <script>
    // Get all the ticket photos
    var ticketPhotos = document.querySelectorAll(".ticket-photo");

    // Loop through each ticket photo
    ticketPhotos.forEach(function(photo) {
        // Add click event listener to each photo
        photo.addEventListener('click', function() {
            // Set the source of the modal image to the clicked photo's source
            document.getElementById('myModal').style.display = 'block';
            document.getElementById('img01').src = this.src;
        });
    });

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        document.getElementById('myModal').style.display = 'none';
    };

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        var modal = document.getElementById('myModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
</script>




    <!--
    <script>
        // Get the ticket photo and approve button
var ticketPhoto = document.getElementsByClassName("ticket-photo")[0];
var approveButton = document.getElementsByClassName("checkbox-button")[0];

// When the user clicks on the photo, toggle modal display
ticketPhoto.addEventListener('click', function() {
    document.getElementById('myModal').style.display = 'block';
    document.getElementById('img01').src = this.src;
});

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    document.getElementById('myModal').style.display = 'none';
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    var modal = document.getElementById('myModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
    </script>-->
</body>
</html>
