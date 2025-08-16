<?php
session_start();
include 'include/config.php';

header('Content-Type: application/json');

// Only allow logged-in students
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$student_id = $_SESSION['user_id'];
$section = $_POST['section'] ?? '';

if (!$section) {
    echo json_encode(['success' => false, 'message' => 'Section is required']);
    exit;
}

// Confirm the user is a student in DB
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'student'");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid student']);
    exit;
}

// Prevent duplicate enrollments that are pending or approved
$stmt = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND section = ? AND status IN ('pending', 'approved')");
$stmt->bind_param("is", $student_id, $section);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already enrolled or pending']);
    exit;
}

// Insert the pending enrollment explicitly setting status and timestamp
$stmt = $conn->prepare("INSERT INTO enrollments (student_id, section, status, created_at) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param("is", $student_id, $section);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
