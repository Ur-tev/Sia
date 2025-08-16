<?php
session_start();
include 'include/config.php';

// Only allow department-head role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'department-head') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$course = isset($_GET['course']) ? trim($_GET['course']) : '';

if ($course === '') {
    echo json_encode([]);
    exit;
}

// Fetch students where users.course = $course
$stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'student' AND course = ? ORDER BY username ASC");
$stmt->bind_param("s", $course);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();

header('Content-Type: application/json');
echo json_encode($students);
