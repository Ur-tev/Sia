<?php
session_start();
include 'include/config.php';

// (Optional) Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch user list
$users = [];
$result = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch user counts per day (last 7 days)
$chartData = [];
$result = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM users
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) DESC
    LIMIT 7
");
while ($row = $result->fetch_assoc()) {
    $chartData[] = $row;
}
$chartData = array_reverse($chartData); // Oldest first for chart

// Calculate percentage growth compared to previous day
$growthPercent = 0;
if (count($chartData) >= 2) {
    $yesterday = $chartData[count($chartData) - 2]['count'];
    $today = $chartData[count($chartData) - 1]['count'];
    if ($yesterday > 0) {
        $growthPercent = (($today - $yesterday) / $yesterday) * 100;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar styling */
        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .sidebar .logout {
            margin-top: auto;
            background-color: #c0392b;
            text-align: center;
        }
        .sidebar .logout:hover {
            background-color: #e74c3c;
        }

        /* Main content area */
        .main {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
        }
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .growth {
            font-size: 1.2em;
            color: <?= ($growthPercent >= 0) ? 'green' : 'red' ?>;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="add_roles.php">➕ Add Roles</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <h1>📊 Admin Dashboard</h1>
    <div class="dashboard">
        <div class="card">
            <h2>Users Added (Last 7 Days)</h2>
            <canvas id="usersChart"></canvas>
            <p class="growth">
                <?= ($growthPercent >= 0 ? '▲' : '▼') . round($growthPercent, 2) ?>% from yesterday
            </p>
        </div>
        <div class="card">
            <h2>List of Users</h2>
            <table>
                <tr><th>ID</th><th>Username</th><th>Role</th><th>Date Added</th></tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('usersChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($chartData, 'date')) ?>,
        datasets: [{
            label: 'Users Added',
            data: <?= json_encode(array_column($chartData, 'count')) ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
</body>
</html>
