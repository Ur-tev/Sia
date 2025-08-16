<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$student_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
        }

        /* Navbar */
        .navbar {
            background-color: #34495e;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar .student-id {
            font-weight: bold;
            font-size: 16px;
        }

        .navbar a.logout {
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .navbar a.logout:hover {
            background-color: #c0392b;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background-color: #2c3e50;
            color: white;
            z-index: 999;
        }

        .sidebar-content {
            padding-top: 70px; /* Push below navbar */
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
            color: #ecf0f1;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #1abc9c;
        }

        .sidebar a span {
            margin-right: 10px;
        }

        /* Content */
        .content {
            margin-left: 240px;
            padding: 100px 30px 30px;
        }

        .content h1 {
            font-size: 28px;
            color: #2c3e50;
        }

        .content p {
            font-size: 16px;
            color: #555;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .content {
                margin-left: 0;
                padding: 100px 20px 20px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="student-id">Student ID: <?php echo htmlspecialchars($student_id); ?></div>
    <a href="logout.php" class="logout">Logout</a>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-content">
        <h2>📚 Menu</h2>
        <a href="student_dashboard.php"><span>🏠</span>Home</a>
        <a href="enrollment.php"><span>📝</span>Enrollment</a>
        <a href="accountabilities.php"><span>💰</span>Accountabilities</a>
        <a href="grades.php"><span>📊</span>Grades View</a>
    </div>
</div>

<!-- Content -->
<div class="content">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Use the menu on the left to navigate your academic records, check your enrollment, view grades, and manage your accountabilities.</p>
</div>

</body>
</html>
