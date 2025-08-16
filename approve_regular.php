<?php
session_start();
include 'include/config.php';

// Only allow registrars
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    header('Location: login.php');
    exit;
}

$enrollment_id = $_POST['enrollment_id'] ?? null;

if (!$enrollment_id) {
    die('Enrollment ID missing');
}

// Update the enrollment status to 'approved'
$stmt = $conn->prepare("UPDATE enrollments SET status = 'approved' WHERE id = ?");
$stmt->bind_param("i", $enrollment_id);

if ($stmt->execute()) {
    header('Location: pending_enrollments.php'); // redirect back to the list page
    exit;
} else {
    die('Error updating enrollment: ' . $conn->error);
}
