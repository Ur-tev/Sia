<?php
session_start();
include 'include/config.php';

// Check logged in student
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = intval($_SESSION['user_id']);

$sql = "SELECT year_level, semester, subject_code, description, units, status, prereq, can_enroll 
        FROM student_subjects WHERE student_id = ? ORDER BY year_level, semester, subject_code";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $student_id);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Enrollment - Student Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; display:flex; min-height:100vh; }
        .sidebar {
            width: 220px; background:#2c3e50; color:#fff; padding-top:20px;
            display:flex; flex-direction: column; position: fixed; height: 100vh;
        }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a {
            color:#fff; text-decoration:none; padding: 15px 20px;
            display:flex; align-items:center; gap:10px; transition: background 0.3s ease;
        }
        .sidebar a:hover { background: #34495e; }
        .content {
            margin-left: 220px; padding: 20px; flex-grow: 1; background: #ecf0f1; overflow-y:auto;
        }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 8px; text-align: left; }
        thead { background: #ddd; }

        /* Enroll Button */
        #enrollBtn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 15px 25px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            z-index: 1000;
        }


        #choiceModal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        #choiceModal .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            min-width: 300px;
        }

        #choiceModal button {
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .regular-btn { background-color: #2980b9; color: white; }
        .irregular-btn { background-color: #e67e22; color: white; }
        .cancel-btn {
            margin-top: 15px;
            background: none;
            color: #777;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Student Portal</h2>
    <a href="student_dashboard.php">🏠 Dashboard</a>
    <a href="enrollment.php" style="background: #34495e;">📝 Enrollment</a>
    <a href="accountabilities.php">💰 Accountabilities</a>
    <a href="grades.php">📊 Grades View</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">
    <h1>Your Enrolled Subjects</h1>

    <table>
        <thead>
            <tr>
                <th>Year Level</th>
                <th>Semester</th>
                <th>Code</th>
                <th>Description</th>
                <th>Units</th>
                <th>Status</th>
                <th>Prereq</th>
                <th>Can Enroll</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($subjects)): ?>
                <?php foreach ($subjects as $subj): ?>
                    <tr>
                        <td><?= htmlspecialchars($subj['year_level']) ?></td>
                        <td><?= htmlspecialchars($subj['semester']) ?></td>
                        <td><?= htmlspecialchars($subj['subject_code']) ?></td>
                        <td><?= htmlspecialchars($subj['description']) ?></td>
                        <td><?= htmlspecialchars($subj['units']) ?></td>
                        <td><?= htmlspecialchars($subj['status']) ?></td>
                        <td><?= htmlspecialchars($subj['prereq']) ?></td>
                        <td><?= $subj['can_enroll'] ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No enrolled subjects found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Enroll Now Button -->
<button id="enrollBtn">Enroll Now</button>

<!-- Modal for Choosing Regular or Irregular -->
<div id="choiceModal">
    <div class="modal-content">
        <h2>Select Enrollment Type</h2>
        <button class="regular-btn" onclick="location.href='regular.php'">Regular</button>
        <button class="irregular-btn" onclick="location.href='irregular.php'">Irregular</button>
        <br />
        <button class="cancel-btn" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
    const modal = document.getElementById('choiceModal');
    const enrollBtn = document.getElementById('enrollBtn');

    enrollBtn.onclick = function() {
        modal.style.display = 'flex';
    };

    function closeModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
</script>

</body>
</html>
