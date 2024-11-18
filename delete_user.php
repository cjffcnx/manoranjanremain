<?php
$conn = mysqli_connect("localhost", "root", "", "manoranjan_db");

if (!$conn) {
    die("Cannot connect to database: " . mysqli_connect_error());
}

if (isset($_POST['user_email']) && isset($_POST['comment_id'])) {
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $comment_id = mysqli_real_escape_string($conn, $_POST['comment_id']);
    
    // Delete the specific comment using comment_id
    $delete_query = "DELETE FROM comments WHERE id='$comment_id' AND user_email='$user_email'";

    if (mysqli_query($conn, $delete_query)) {
        echo "Comment deleted successfully.";
    } else {
        echo "Error deleting comment: " . mysqli_error($conn);
    }
} else {
    echo "Required data not provided.";
}
?>
