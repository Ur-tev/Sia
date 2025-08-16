<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'department-head') {
    header("Location: login.php");
    exit;
}

$dept_head_id = $_SESSION['user_id'] ?? 0;

// Grade point calculation function (same scale)
function getPointsEquivalent($grade) {
    if ($grade >= 97) return "1.0";
    if ($grade >= 94) return "1.25";
    if ($grade >= 91) return "1.5";
    if ($grade >= 88) return "1.75";
    if ($grade >= 85) return "2.0";
    if ($grade >= 82) return "2.25";
    if ($grade >= 79) return "2.5";
    if ($grade >= 76) return "2.75";
    if ($grade >= 75) return "3.0";
    return "5.0"; // fail
}

function getStatus($grade) {
    return ($grade >= 75) ? "Passed" : "Failed";
}

// Fetch grades waiting for department head approval
$grades = $conn->query("SELECT g.*, u.username as student_name, t.username as teacher_name
                       FROM grades g
                       JOIN users u ON g.student_id = u.id
                       JOIN users t ON g.teacher_id = t.id
                       WHERE g.dept_head_approved = 0");

if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE grades 
                  SET dept_head_approved = 1, dept_head_id = $dept_head_id, dept_head_approved_at = NOW() 
                  WHERE id = $id");
    header("Location: department_head_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Department Head Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #28a745;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f1f8f5;
        }
        a {
            color: #28a745;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h2>Department Head Dashboard</h2>

<table>
    <tr>
        <th>Student</th>
        <th>Teacher</th>
        <th>Subject</th>
        <th>Prelims</th>
        <th>Midterms</th>
        <th>Pre-Finals</th>
        <th>Finals</th>
        <th>Final Grade</th>
        <th>Units</th>
        <th>Points</th>
        <th>Status</th>
        <th>Approval Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $grades->fetch_assoc()) {
        $finalGrade = floatval($row['final_grade']);
        $points = getPointsEquivalent($finalGrade);
        $status = getStatus($finalGrade);
        $units = isset($row['units']) ? $row['units'] : 'N/A';
    ?>
    <tr>
        <td><?= htmlspecialchars($row['student_name']) ?></td>
        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
        <td><?= htmlspecialchars($row['prelims']) ?></td>
        <td><?= htmlspecialchars($row['midterms']) ?></td>
        <td><?= htmlspecialchars($row['prefinals']) ?></td>
        <td><?= htmlspecialchars($row['finals']) ?></td>
        <td><?= number_format($finalGrade, 2) ?></td>
        <td><?= htmlspecialchars($units) ?></td>
        <td><?= $points ?></td>
        <td><?= $status ?></td>
        <td><?= $row['dept_head_approved'] ? 'Approved' : 'Submitted' ?></td>
        <td><a href="?approve=<?= $row['id'] ?>" onclick="return confirm('Approve this grade?');">Approve</a></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
