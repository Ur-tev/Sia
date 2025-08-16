<?php
// admission_details.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if needed

include 'include/config.php'; // Your DB config file

// Function to send email using PHPMailer SMTP
function sendEmailSMTP($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'stevegramatica2@gmail.com'; // SMTP username
        $mail->Password   = 'your-app-password'; // SMTP password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('registrar@yourdomain.com', 'Registrar Office');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Validate input
if (!isset($_POST['application_id']) || !isset($_POST['action'])) {
    die("Invalid request. Required POST parameters missing.");
}

$application_id = intval($_POST['application_id']);
$action = $_POST['action']; // 'approve' or 'reject'

// Fetch application data from database
$sql = "SELECT * FROM admission_applications WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $application_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Application not found.");
}

$application = mysqli_fetch_assoc($result);

// Debug to see what fields are fetched:
// Uncomment this if you want to check field names.
// var_dump(array_keys($application));
// exit;

// Check email existence
// Adjust 'email_address' to your actual DB column name, e.g., 'email' or 'applicant_email'
$email_field = 'email';  // <-- Change this to your actual email column name

if (!isset($application[$email_field]) || empty($application[$email_field])) {
    die("Applicant email address not found.");
}

$applicantEmail = $application[$email_field];

if ($action === 'approve') {
    // Update status to approved
    $sql_update = "UPDATE admission_applications SET status = 'approved' WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    if (!$stmt_update) {
        die("Prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_update, "i", $application_id);
    mysqli_stmt_execute($stmt_update);

    // Email content
    $subject = "Your Admission Application Has Been Approved";
    $message = "
        <html>
        <head><title>Application Approved</title></head>
        <body>
            <p>Dear {$application['first_name']} {$application['last_name']},</p>
            <p>Congratulations! Your admission application has been approved.</p>
            <p>We will contact you with further instructions soon.</p>
            <br>
            <p>Best regards,<br>Registrar Office</p>
        </body>
        </html>
    ";

    if (sendEmailSMTP($applicantEmail, $subject, $message)) {
        echo "Application approved and email sent to applicant.";
    } else {
        echo "Application approved but failed to send email.";
    }

} elseif ($action === 'reject') {
    // Email rejection first
    $subject = "Your Admission Application Has Been Rejected";
    $message = "
        <html>
        <head><title>Application Rejected</title></head>
        <body>
            <p>Dear {$application['first_name']} {$application['last_name']},</p>
            <p>We regret to inform you that your admission application has been rejected.</p>
            <p>If you have questions, please contact the Registrar Office.</p>
            <br>
            <p>Best regards,<br>Registrar Office</p>
        </body>
        </html>
    ";

    if (sendEmailSMTP($applicantEmail, $subject, $message)) {
        // Delete application record
        $sql_delete = "DELETE FROM admission_applications WHERE id = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        if (!$stmt_delete) {
            die("Prepare failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_delete, "i", $application_id);
        mysqli_stmt_execute($stmt_delete);

        echo "Application rejected, email sent, and record deleted.";
    } else {
        echo "Failed to send rejection email. Application not deleted.";
    }

} else {
    die("Invalid action.");
}

// Close connections
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
