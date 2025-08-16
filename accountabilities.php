<?php
session_start();
include 'include/config.php';

// Only students allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all accountabilities for this student (without amount)
$stmt = $conn->prepare("SELECT description, status, category, created_at FROM accountabilities WHERE student_user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Accountabilities</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
th { background-color: #2c3e50; color: white; }
tr:nth-child(even) { background-color: #f2f2f2; }
</style>
</head>
<body>

<h1>My Accountabilities</h1>
<table>
    <thead>
        <tr>
            <th>Description</th>
            <th>Status</th>
            <th>Category</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['category'])) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">No accountabilities found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<a href="student_dashboard.php">Back to Dashboard</a>

</body>
</html>
