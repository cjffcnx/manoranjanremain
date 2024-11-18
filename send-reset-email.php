<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "adminlogin");
if (!$conn) {
    die("Cannot connect to database: " . mysqli_connect_error());
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // PHPMailer autoload

// Check if the email is set from a POST request
if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the admin table
    $query = "SELECT * FROM admin WHERE Email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Email exists, generate a reset token
        $token = bin2hex(random_bytes(50)); // Generate random token
        
        // Update the token in the database
        $update_query = "UPDATE admin SET reset_token='$token' WHERE Email='$email'";
        if (mysqli_query($conn, $update_query)) {
            // Construct the reset link
            $resetLink = "http://localhost/projectsocialmedia/reset-password.php?token=$token&email=$email";

            // Send the reset link via PHPMailer
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();                                     
                $mail->Host = 'smtp.gmail.com';                       
                $mail->SMTPAuth = true;                               
                $mail->Username   = 'manoranjaninproject@gmail.com'; // Your Gmail address
                $mail->Password   = 'grzrfaffrwngabwz'; // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('manoranjaninproject@gmail.com', 'Manoranjan');
                $mail->addAddress($email);  

                // Email content
                $mail->isHTML(true);  // Set email format to HTML
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "
                    <p>Click the link below to reset your password:</p>
                    <a href='$resetLink'>$resetLink</a>
                    <p>If you did not request a password reset, please ignore this email.</p>
                ";

                $mail->send();
                echo "Password reset link has been sent to your email.";
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    } else {
        echo "No account found with that email address.";
    }
}

mysqli_close($conn); // Close the database connection
?>