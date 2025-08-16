<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'department-head') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$yearLevels = ['1st', '2nd', '3rd', '4th'];

$yearData = [];
foreach ($yearLevels as $year) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM student_subjects WHERE year_level = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        $row = $res->fetch_assoc();
        $yearData[$year] = $row['total'] ?? 0;
    } else {
        $yearData[$year] = 0;
    }
    $stmt->close();
}

$today = date('Y-m-d');
$addedTodayData = [];
foreach ($yearLevels as $year) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count_today FROM student_subjects WHERE year_level = ? AND DATE(created_at) = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ss", $year, $today);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        $row = $res->fetch_assoc();
        $addedTodayData[$year] = $row['count_today'] ?? 0;
    } else {
        $addedTodayData[$year] = 0;
    }
    $stmt->close();
}

// Fetch all subjects list
$subjectsList = $conn->query("
    SELECT student_id, subject_code, description, year_level, semester, created_at 
    FROM student_subjects 
    ORDER BY year_level ASC, created_at DESC
");
if (!$subjectsList) {
    die("Query failed: (" . $conn->errno . ") " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Department Head Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background: #f4f6f9;
        }
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
        .main {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }
        h1, h2, h3 { margin-top: 0; }
        .chart-container { width: 60%; margin: auto; background: white; padding: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Dept. Panel</h2>
    <a href="department_head_dashboard.php">📊 Dashboard</a>
    <a href="add_subject.php">➕ Add Subject</a>
    <a href="department_grades.php">List of Grades</a>
    <a href="?logout=1" class="logout">🚪 Logout</a>
</div>

<div class="main">
    <h2>📊 Subjects Count per Year Level</h2>
    <div class="chart-container">
        <canvas id="yearChart"></canvas>
    </div>

    <h3>📋 List of Added Subjects</h3>
    <table>
        <tr>
            <th>Student ID</th>
            <th>Subject Code</th>
            <th>Description</th>
            <th>Year Level</th>
            <th>Semester</th>
            <th>Date Added</th>
        </tr>
        <?php if ($subjectsList->num_rows > 0): ?>
            <?php while ($row = $subjectsList->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['subject_code']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['year_level']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No subjects added yet.</td></tr>
        <?php endif; ?>
    </table>
</div>

<script>
const ctx = document.getElementById('yearChart').getContext('2d');

const addedTodayData = [
    <?= (int)($addedTodayData['1st'] ?? 0) ?>,
    <?= (int)($addedTodayData['2nd'] ?? 0) ?>,
    <?= (int)($addedTodayData['3rd'] ?? 0) ?>,
    <?= (int)($addedTodayData['4th'] ?? 0) ?>
];

const yearTotals = [
    <?= (int)($yearData['1st'] ?? 0) ?>,
    <?= (int)($yearData['2nd'] ?? 0) ?>,
    <?= (int)($yearData['3rd'] ?? 0) ?>,
    <?= (int)($yearData['4th'] ?? 0) ?>
];

// If no subjects added today, hide line by making color transparent
const addedTodayLineColor = addedTodayData.some(c => c > 0) ? 'green' : 'transparent';

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        datasets: [
            {
                label: 'Total Subjects',
                data: yearTotals,
                backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#E91E63'],
                order: 1
            },
            {
                label: 'Subjects Added Today',
                type: 'line',
                data: addedTodayData,
                borderColor: addedTodayLineColor,
                backgroundColor: addedTodayLineColor,
                fill: false,
                tension: 0.4,
                order: 2,
                yAxisID: 'y'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            x: {
                type: 'category',
                labels: ['1st Year', '2nd Year', '3rd Year', '4th Year']
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

</body>
</html>
