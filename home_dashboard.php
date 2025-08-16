<?php
session_start();
include 'include/config.php';

// Only registrar can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Query to get status counts
$statusCounts = ['pending' => 0, 'resolved' => 0, 'failed' => 0];
$query = "SELECT status, COUNT(*) as count FROM accountabilities WHERE category = 'document' GROUP BY status";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $key = strtolower($row['status']);
    if (isset($statusCounts[$key])) {
        $statusCounts[$key] = $row['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registrar - Home Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    display: flex;
    height: 100vh;
  }
  .sidebar {
    width: 220px;
    background-color: #2c3e50;
    color: white;
    display: flex;
    flex-direction: column;
    padding: 20px;
    box-sizing: border-box;
  }
  .sidebar h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 20px;
  }
  .sidebar a {
    color: white;
    text-decoration: none;
    margin-bottom: 15px;
    font-size: 16px;
    padding: 8px 12px;
    border-radius: 4px;
    display: block;
  }
  .sidebar a:hover {
    background-color: #34495e;
  }

  .main-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
  }

  .user-info {
    margin-bottom: 20px;
  }

  canvas {
    background: white;
    border: 1px solid #ccc;
    padding: 15px;
    border-radius: 5px;
  }
</style>
</head>
<body>

<div class="sidebar">
  <h2>Menu</h2>
  <a href="home_dashboard.php">Home Dashboard</a>
  <a href="admission_list.php">Admission Details</a>
  <a href="adding_documents.php">Adding Documents</a>
  <a href="registrar_grades.php">Finalize of Grades</a>
  <a href="registrar_regular.php">Registrar of Regular</a>
  <a href="dashboard_graph.php">Dashboard Graph</a>
  <a href="?logout=1">Logout</a>
</div>

<div class="main-content">
  <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
  <p class="user-info">Role: <strong><?= $_SESSION['role'] ?></strong></p>

  <h2>Document Accountabilities Overview</h2>
  <canvas id="accountabilityChart" width="600" height="300"></canvas>

  <script>
    const ctx = document.getElementById('accountabilityChart').getContext('2d');
    const accountabilityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Resolved', 'Failed'],
            datasets: [{
                label: 'Document Accountabilities',
                data: [
                    <?= $statusCounts['pending'] ?>,
                    <?= $statusCounts['resolved'] ?>,
                    <?= $statusCounts['failed'] ?>
                ],
                backgroundColor: ['orange', 'green', 'red'],
                borderColor: ['darkorange', 'darkgreen', 'darkred'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
  </script>
</div>

</body>
</html>
