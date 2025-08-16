<?php
session_start();
include 'include/config.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    header("Location: login.php");
    exit;
}

$registrar_id = $_SESSION['user_id'] ?? 0;

// Handle finalize action
if (isset($_GET['finalize'])) {
    $id = intval($_GET['finalize']);
    $conn->query("UPDATE grades 
                  SET registrar_finalized = 1, registrar_id = $registrar_id, registrar_finalized_at = NOW()
                  WHERE id = $id");
    // No redirect - stay on dashboard
}

// Fetch grades approved by dept head but not yet finalized
$grades = $conn->query("SELECT g.*, u.username AS student_name, t.username AS teacher_name
                        FROM grades g
                        JOIN users u ON g.student_id = u.id
                        JOIN users t ON g.teacher_id = t.id
                        WHERE g.dept_head_approved = 1 AND g.registrar_finalized = 0");

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Dashboard</title>
    <style>
        /* Layout */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background: #f4f4f4;
        }
        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .sidebar h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
            border-bottom: 1px solid #34495e;
            padding-bottom: 10px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-weight: 600;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            flex: 1;
            padding: 20px 30px;
            overflow-y: auto;
            background: white;
        }

        /* Table styling */
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
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
            background-color: #f9f9f9;
        }
        h2.page-title {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }
        a.action-link {
            color: #007BFF;
            text-decoration: none;
        }
        a.action-link:hover {
            text-decoration: underline;
        }

        /* Responsive sidebar */
        @media (max-width: 700px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                flex-direction: row;
                overflow-x: auto;
                padding: 10px 5px;
                gap: 10px;
            }
            .sidebar h2 {
                display: none;
            }
            .sidebar a {
                flex: 1;
                text-align: center;
                padding: 8px 5px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Menu</h2>
        <a href="registrar_dashboard.php">Registrar Dashboard</a>
        <a href="admission_list.php">Admission Details</a>
        <a href="adding_documents.php">Adding Documents</a>
        <a href="registrar_grades.php">Finalize of Grades</a>
        <a href="registrar_regular.php">Registrar of Regular</a>
        <a href="?logout=1">Logout</a>
    </div>

    <div class="content">
        <h2 class="page-title">Registrar Dashboard</h2>

        <table>
            <tr>
                <th>Student</th>
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
                <th>Approved by Dept Head</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $grades->fetch_assoc()):
                $finalGrade = floatval($row['final_grade']);
                $points = getPointsEquivalent($finalGrade);
                $status = getStatus($finalGrade);
                $units = isset($row['units']) ? $row['units'] : 'N/A';
            ?>
            <tr>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
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
                <td><?= $row['dept_head_approved'] ? 'Yes' : 'No' ?></td>
                <td><a class="action-link" href="?finalize=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to finalize this grade?');">Finalize</a></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
