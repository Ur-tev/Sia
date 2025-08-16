<?php
session_start();
include 'include/config.php';

// Check if user is registrar
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;
$email = $_POST['email'] ?? null;

if (!$id || !$status || !$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

// Validate status value (only allow approved or rejected)
if (!in_array($status, ['approved', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

// Update status in the database
$stmt = $conn->prepare("UPDATE admission_applications SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Database update failed']);
    exit;
}

// Send email notification to applicant
$subject = $status === 'approved' ? "Your Admission Application is Approved" : "Your Admission Application has been Rejected";
$message = $status === 'approved' ?
    "Dear Applicant,\n\nCongratulations! Your admission application has been approved.\n\nRegards,\nRegistrar Office" :
    "Dear Applicant,\n\nWe regret to inform you that your admission application has been rejected.\n\nRegards,\nRegistrar Office";

$headers = "From: no-reply@yourdomain.com\r\n";
$headers .= "Reply-To: registrar@yourdomain.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$mail_sent = mail($email, $subject, $message, $headers);

if (!$mail_sent) {
    // You can log this error but still return success if DB updated
    error_log("Failed to send email to $email");
}

echo json_encode(['success' => true]);
?>
