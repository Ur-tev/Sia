<?php
session_start();
include 'include/config.php';

// Only allow department-head role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'department-head') {
    header("Location: login.php");
    exit;
}

$message = "";

// Fetch courses for dropdown (distinct courses from users table)
$courses = [];
$course_result = $conn->query("SELECT DISTINCT course FROM users WHERE course <> '' ORDER BY course ASC");
if ($course_result) {
    while ($row = $course_result->fetch_assoc()) {
        $courses[] = $row['course'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = intval($_POST['student_id']);
    $year_level = trim($_POST['year_level']);
    $semester = trim($_POST['semester']);
    $code = trim($_POST['code']);
    $description = trim($_POST['description']);
    $units = intval($_POST['units']);
    $status = trim($_POST['status']);
    $prereq = trim($_POST['prereq']);
    $can_enroll = ($_POST['can_enroll'] === 'Yes') ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO student_subjects (student_id, year_level, semester, subject_code, description, units, status, prereq, can_enroll) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("isssssiss", $student_id, $year_level, $semester, $code, $description, $units, $status, $prereq, $can_enroll);

    if ($stmt->execute()) {
        $message = "✅ Subject added successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Department Head - Add Subject</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background: #f4f6f9;
            height: 100vh;
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
            overflow-y: auto;
        }
        form {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 12px 20px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #219150;
        }
        .message {
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Dept. Panel</h2>
    <a href="department_head_dashboard.php">📊 Dashboard</a>
    <a href="add_subject.php" style="background-color:#34495e;">➕ Add Subject</a>
    <a href="department_grades.php">List of Grades</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h3>Add Subject to Student</h3>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="course_id">Select Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="">-- Select Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course) ?>"><?= htmlspecialchars($course) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="student_id">Select Student:</label>
        <select id="student_id" name="student_id" required>
            <option value="">-- Select Student --</option>
            <!-- Students will be loaded dynamically based on course selection -->
        </select>

        <label for="year_level">Year Level:</label>
        <select id="year_level" name="year_level" required>
            <option value="">-- Select Year --</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
            <option value="4th Year">4th Year</option>
        </select>

        <label for="semester">Semester:</label>
        <select id="semester" name="semester" required>
            <option value="">-- Select Semester --</option>
            <option value="1st Semester">1st Semester</option>
            <option value="2nd Semester">2nd Semester</option>
        </select>

        <label for="code">Code:</label>
        <input type="text" id="code" name="code" required>

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" required>

        <label for="units">Units:</label>
        <input type="number" id="units" name="units" min="0" required>

        <label for="status">Status:</label>
        <input type="text" id="status" name="status" required>

        <label for="prereq">Prerequisite:</label>
        <input type="text" id="prereq" name="prereq">

        <label for="can_enroll">Can Enroll:</label>
        <select id="can_enroll" name="can_enroll" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
        </select>

        <button type="submit">Add Subject</button>
    </form>
</div>

<script>
document.getElementById('course_id').addEventListener('change', function() {
    const course = this.value;
    const studentSelect = document.getElementById('student_id');

    // Clear current options except placeholder
    studentSelect.innerHTML = '<option value="">-- Select Student --</option>';

    if (!course) return;

    fetch('fetch_students.php?course=' + encodeURIComponent(course))
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                data.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = student.username;
                    studentSelect.appendChild(option);
                });
            }
        })
        .catch(err => {
            console.error('Failed to fetch students:', err);
        });
});
</script>

</body>
</html>
