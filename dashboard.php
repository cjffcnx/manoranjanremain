<?php
include('authentication.php');  // This checks if the user is logged in
$page_title = "Dashboard Page";

include('includes/header.php');
include('includes/navbar.php');
?>
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 ">
            <?php

if(isset($_SESSION['status']))
{
    echo "<div class='alert alert-warning'>".$_SESSION['status']."</div>";
    unset($_SESSION['status']);  // Clear the message after displaying it
}
?>

                <div class="card">
                    <div class="card-header">
                        <h4> User Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <h2>Access when you are logged in</h2>
                        <hr>
                        <h5>Username:<?=$_SESSION['auth_user']['username']?></h5>
                        <h5>Email:<?=$_SESSION['auth_user']['email']?></h5>
                        <h5>Phone:<?=$_SESSION['auth_user']['phone']?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
    <style>
        /* Add styles here for form, table, etc. */
        .calendar { display: flex; flex-direction: column; margin: 20px; }
        .event { display: flex; justify-content: space-between; padding: 10px; background-color: #f4f4f4; margin-bottom: 10px; }
        .event-actions button { margin-left: 10px; }
    </style>
</head>
<body>
    <h1 style="text-align:Center;">Event Dashboard</h1>

    <!-- Form to Add Events -->
    <form class="cform" action="dashboard.php" method="POST" autocomplete="off">
        <input type="text" name="event_name" placeholder="Event Name" required>
        <input type="date" name="event_date" required>
        <textarea name="event_description" placeholder="Event Description" required></textarea>
        <button class="eb" type="submit" name="add_event">Add Event</button>
    </form>

    <style>
.cform{
    display:flex;
    justify-content:space-evenly;
    align-items:Center;
    margin:0 10px;
    padding:0 10px;
    
}

.eb{
    background-color: #007bff; /* Blue background */
    color: white; /* White text */
    border: none; /* Remove border */
    padding: 10px 20px; /* Add padding for more space */
    border-radius: 5px; /* Round the corners */
    cursor: pointer; /* Change cursor to pointer on hover */
    font-size: 16px; /* Increase font size */
    font-weight: bold;
}
        </style>

    <!-- Event List -->
    <div class="calendar">
        <?php
        // Display events from database
        include 'connect.php'; // Ensure this file contains database connection code

        $query = "SELECT * FROM events ORDER BY event_date ASC";
        $result = mysqli_query($conn, $query);

        while($row = mysqli_fetch_assoc($result)) {
            echo '<div class="event" style="background-color:aqua' . randomColor() . '">';
            echo '<div><strong>' . $row['event_name'] . '</strong><br>' . $row['event_date'] . '<br>' . $row['event_description'] . '</div>';
            echo '<div class="event-actions">
                    <form style="display:inline;" method="POST" action="dashboard.php">
                        <input type="hidden" name="event_id" value="' . $row['id'] . '">
                        <button style="padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 10px; background-color:lightgreen; color:white; font-weight:500;" type="submit" name="edit_event">Edit</button>
                    </form>
                    <form style="display:inline;" method="POST" action="dashboard.php">
                        <input type="hidden" name="event_id" value="' . $row['id'] . '">
                        <button style="padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 10px; background-color:red; color:white; font-weight:500;" type="submit" name="delete_event">Delete</button>
                    </form>';

            // Share button to trigger sharing options
            echo '<button class="share-btn" style="padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    background-color:#007bff; color:white; font-weight:500;" onclick="shareEvent(\'' . $row['event_name'] . '\', \'' . $row['event_description'] . '\', window.location.href)">Share</button>';

            echo '</div>';
            echo '</div>';
        }

        // Function to generate random color
        function randomColor() {
            return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
        ?>
    </div>

    <!-- JS for Share Button -->
    <script>
    function shareEvent(title, text, url) {
        if (navigator.share) {
            navigator.share({
                title: title,
                text: text,
                url: url
            }).then(() => {
                console.log('Event shared successfully!');
            }).catch((error) => {
                console.error('Error sharing event:', error);
            });
        } else {
            alert('Web Share API is not supported on this browser.');
        }
    }
    </script>

</body>
</html>

<?php
include 'connect.php'; // Database connection file

// Add event logic
if (isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_description = $_POST['event_description'];

    $sql = "INSERT INTO events (event_name, event_date, event_description) VALUES ('$event_name', '$event_date', '$event_description')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Event added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding event.');</script>";
    }
}

// Delete event logic
if (isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];

    $sql = "DELETE FROM events WHERE id=$event_id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Event deleted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting event.');</script>";
    }
}

// Edit event logic
if (isset($_POST['edit_event'])) {
    $event_id = $_POST['event_id'];

    // Get event details
    $query = "SELECT * FROM events WHERE id=$event_id";
    $result = mysqli_query($conn, $query);
    $event = mysqli_fetch_assoc($result);

    // Show a form with pre-filled values to edit the event
    echo '<form style="display:flex; justify-content:space-evenly;" action="dashboard.php" method="POST">
            <input type="hidden" name="event_id" value="' . $event_id . '">
            <input type="text" name="event_name" value="' . $event['event_name'] . '" required>
            <input type="date" name="event_date" value="' . $event['event_date'] . '" required>
            <textarea name="event_description" required>' . $event['event_description'] . '</textarea>
            <button style="padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px; margin-right: 10px;" type="submit" name="update_event">Update Event</button>
          </form>';
}

// Update event logic
if (isset($_POST['update_event'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_description = $_POST['event_description'];

    $sql = "UPDATE events SET event_name='$event_name', event_date='$event_date', event_description='$event_description' WHERE id=$event_id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Event updated successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating event.');</script>";
    }
}


?>






<?php include('includes/footer.php'); ?>
