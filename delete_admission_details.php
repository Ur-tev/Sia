<?php
session_start();
include 'include/config.php';

// Check for registrar role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;

    if (!$id) {
        http_response_code(400);
        echo "Missing or invalid ID";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM admission_applications WHERE id = ?");
    
    if (!$stmt) {
        http_response_code(500);
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Deleted";
    } else {
        http_response_code(500);
        echo "Failed to delete application. Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
