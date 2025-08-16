<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Irregular Enrollment - Student Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 900px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 30px; }
        .info { font-size: 18px; text-align: center; margin-bottom: 20px; }
        a.btn { display: inline-block; padding: 10px 20px; background: #e67e22; color: white; text-decoration: none; border-radius: 5px; }
        a.btn:hover { background: #d35400; }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome to Irregular Enrollment</h1>
    <p class="info">You are now enrolling as an <strong>irregular student</strong>.</p>

    <p class="info">You may manually select subjects based on prerequisites and availability.</p>

    <!-- Placeholder for subject selection logic -->

    <div style="text-align: center;">
        <a class="btn" href="student_dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
