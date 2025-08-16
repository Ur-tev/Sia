<?php
session_start();
include 'include/config.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Only allow registrars
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    header('Location: login.php');
    exit;
}

// Query pending enrollments with student username
$pendingQuery = "
    SELECT 
        e.id,
        u.username,
        e.section,
        e.status,
        e.created_at
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    WHERE e.status = 'pending'
";

$result = $conn->query($pendingQuery);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Enrollments</title>
    <style>
        /* Layout */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            background: #f4f6f9;
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
        h2.page-title {
            color: #2c3e50;
            margin-top: 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #27ae60;
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
        <h2 class="page-title">Pending Enrollments</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Section</th>
                    <th>Requested At</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['section']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <form method="POST" action="approve_regular.php">
                                <input type="hidden" name="enrollment_id" value="<?= $row['id'] ?>">
                                <button type="submit">Approve</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No pending enrollments found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
