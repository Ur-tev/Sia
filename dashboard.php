<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>
<h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
<h2>Your role: <?= htmlspecialchars($role) ?></h2>
<hr>

<?php
switch ($role) {
    case 'student':
        header("Location: student_dashboard.php");
        exit;
    case 'teacher':
        header("Location: teacher_dashboard.php");
        exit;
    case 'treasury':
        header("Location: treasury_dashboard.php");
        exit;
    case 'registrar':
        header("Location: registrar_dashboard.php");
        exit;
    case 'department-head':
        header("Location: department_head_dashboard.php");
        exit;
    case 'admin':
        header("Location: admin_dashboard.php");
        exit;
    default:
        header("Location: login.php?error=invalid_role");
        exit;
}
?>

<p><a href="logout.php">Logout</a></p>
