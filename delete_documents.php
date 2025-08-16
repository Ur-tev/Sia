<?php
session_start();
include 'include/config.php';

if (isset($_GET['id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'registrar') {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM accountabilities WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: adding_documents.php");
exit;
?>
