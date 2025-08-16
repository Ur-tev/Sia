<?php
session_start();
include 'include/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Fetch finalized grades for the logged-in student
$student_id = $_SESSION['user_id'];
$grades = $conn->query("SELECT g.*, t.username AS teacher_name
                        FROM grades g
                        JOIN users t ON g.teacher_id = t.id
                        WHERE g.student_id = $student_id AND g.registrar_finalized = 1");

// Function to calculate grade point
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
    return "5.0"; // failed
}

function getStatus($grade) {
    return ($grade >= 75) ? 'Passed' : 'Failed';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
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
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Student Dashboard</h2>

<table>
    <tr>
        <th>Teacher</th>
        <th>Subject</th>
        <th>Semester</th>
        <th>Prelims</th>
        <th>Midterms</th>
        <th>Pre-Finals</th>
        <th>Finals</th>
        <th>Final Grade</th>
        <th>Units</th>
        <th>Points</th>
        <th>Status</th>
    </tr>
    <?php while ($row = $grades->fetch_assoc()) { 
        $finalGrade = floatval($row['final_grade']);
        $points = getPointsEquivalent($finalGrade);
        $status = getStatus($finalGrade);
        $units = isset($row['units']) ? $row['units'] : 'N/A'; // fallback if units not in table
    ?>
    <tr>
        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
        <td><?= htmlspecialchars($row['semester']) ?></td>
        <td><?= htmlspecialchars($row['prelims']) ?></td>
        <td><?= htmlspecialchars($row['midterms']) ?></td>
        <td><?= htmlspecialchars($row['prefinals']) ?></td>
        <td><?= htmlspecialchars($row['finals']) ?></td>
        <td><?= number_format($finalGrade, 2) ?></td>
        <td><?= htmlspecialchars($units) ?></td>
        <td><?= $points ?></td>
        <td><?= $status ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
