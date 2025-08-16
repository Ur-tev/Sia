<?php
session_start();
include 'include/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['accountability_id']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if ($description === '') {
        echo json_encode(['success' => false, 'message' => 'Description cannot be empty']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE accountabilities SET description = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssi", $description, $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
    $stmt->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
