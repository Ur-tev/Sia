<?php
session_start();
include 'include/config.php';

// Check if logged in as teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

// Debug session info — remove in production
error_log("SESSION user_id: " . ($_SESSION['user_id'] ?? 'none'));
error_log("SESSION role: " . ($_SESSION['role'] ?? 'none'));

// Fetch students
$students = $conn->query("SELECT id, username FROM users WHERE role='student'");

// Subjects list
$subjects = ['Mathematics', 'Science', 'English', 'History', 'Computer'];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate POST inputs and sanitize
    $student_id = intval($_POST['student_id'] ?? 0);
    $semester = trim($_POST['semester'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $equivalent = trim($_POST['equivalent'] ?? '');
    $units = intval($_POST['units'] ?? 0);
    $prelims = floatval($_POST['prelims'] ?? 0);
    $midterms = floatval($_POST['midterms'] ?? 0);
    $prefinals = floatval($_POST['prefinals'] ?? 0);
    $finals = floatval($_POST['finals'] ?? 0);

    // Basic validation
    if ($student_id > 0 && $semester !== '' && $subject !== '' && $units > 0) {
        $final_grade = ($prelims + $midterms + $prefinals + $finals) / 4;

        // Prepare statement with added fields: code, equivalent, units
        $stmt = $conn->prepare("INSERT INTO grades (student_id, teacher_id, semester, subject, code, equivalent, units, prelims, midterms, prefinals, finals, final_grade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $message = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param(
                "iissssiddddd",
                $student_id,
                $_SESSION['user_id'],
                $semester,
                $subject,
                $code,
                $equivalent,
                $units,
                $prelims,
                $midterms,
                $prefinals,
                $finals,
                $final_grade
            );

            $message = $stmt->execute() ? "Grade submitted for review!" : "Error: " . $stmt->error;
            $stmt->close();
        }
    } else {
        $message = "Please fill out all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Teacher Dashboard</title>
<style>
body, html {
    height: 100%;
    margin: 0;
    font-family: Arial, sans-serif;
    background: #eef2f7;
    display: flex;
    justify-content: center;
    align-items: center;
}
.container {
    width: 95%;
    max-width: 1000px;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    overflow-x: auto;
}
.container h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
form select, form input[type="text"], form input[type="number"] {
    width: 100%;
    padding: 10px 12px;
    margin: 6px 0 12px 0;
    border: 1px solid #bbb;
    border-radius: 6px;
    font-size: 14px;
}
button {
    padding: 10px 20px;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    background-color: #007BFF;
    color: #fff;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    background-color: #0056b3;
}
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}
.table-grades {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px;
}
.table-grades th, .table-grades td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    white-space: nowrap;
}
.table-grades th {
    background-color: #007BFF;
    color: white;
    font-weight: 600;
}
.table-grades tr:nth-child(even) {
    background-color: #f9f9f9;
}
.table-grades tr:hover {
    background-color: #e6f0ff;
}
.message {
    text-align: center;
    font-weight: bold;
    margin-bottom: 15px;
    color: green;
}
.error {
    color: red;
}
</style>
</head>
<body>

<div class="container">
    <h2>Teacher Dashboard</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Student:</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php while ($row = $students->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['username']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Semester:</label>
        <select name="semester" id="semester-select" required onchange="showGradeTable()">
            <option value="">-- Select Semester --</option>
            <option value="1st">1st Semester</option>
            <option value="2nd">2nd Semester</option>
            <option value="Summer">Summer</option>
        </select>

        <label>Subject:</label>
        <select name="subject" required>
            <option value="">-- Select Subject --</option>
            <?php foreach ($subjects as $sub): ?>
                <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
            <?php endforeach; ?>
        </select>

        <div id="grades-container" style="display:none;">
            <div class="table-wrapper">
                <table class="table-grades">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Equivalent</th>
                            <th>Units</th>
                            <th>Prelims</th>
                            <th>Midterms</th>
                            <th>Pre-Finals</th>
                            <th>Finals</th>
                            <th>Final Grade</th>
                            <th>Points</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="code" required></td>
                            <td><input type="text" name="equivalent" required></td>
                            <td><input type="number" name="units" min="1" max="5" required></td>
                            <td><input type="number" name="prelims" min="0" max="100" maxlength="3" oninput="limitInputLength(this, 3)" required></td>
                            <td><input type="number" name="midterms" min="0" max="100" maxlength="3" oninput="limitInputLength(this, 3)" required></td>
                            <td><input type="number" name="prefinals" min="0" max="100" maxlength="3" oninput="limitInputLength(this, 3)" required></td>
                            <td><input type="number" name="finals" min="0" max="100" maxlength="3" oninput="limitInputLength(this, 3)" required></td>
                            <td><input type="number" name="final_grade" readonly></td>
                            <td><input type="text" name="points" readonly></td>
                            <td><input type="text" name="status" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <button type="submit">Submit Grade</button>
        </div>
    </form>
</div>

<script>
function showGradeTable() {
    var semester = document.getElementById('semester-select').value;
    var container = document.getElementById('grades-container');
    container.style.display = semester ? 'block' : 'none';
}

// Limit input length and value range
function limitInputLength(el, maxLength) {
    if (el.value.length > maxLength) {
        el.value = el.value.slice(0, maxLength);
    }
    if (parseInt(el.value) > 100) {
        el.value = 100;
    }
    if (parseInt(el.value) < 0) {
        el.value = 0;
    }
    updateFinalGrade();
}

// Calculate final grade, points, and status
function updateFinalGrade() {
    const row = document.querySelector('table.table-grades tbody tr');
    const prelims = parseFloat(row.querySelector('input[name="prelims"]').value) || 0;
    const midterms = parseFloat(row.querySelector('input[name="midterms"]').value) || 0;
    const prefinals = parseFloat(row.querySelector('input[name="prefinals"]').value) || 0;
    const finals = parseFloat(row.querySelector('input[name="finals"]').value) || 0;

    const average = ((prelims + midterms + prefinals + finals) / 4).toFixed(2);
    row.querySelector('input[name="final_grade"]').value = average;

    let status = '';
    if (average >= 75) {
        status = 'Passed';
    } else {
        status = 'Failed';
    }
    row.querySelector('input[name="status"]').value = status;
    row.querySelector('input[name="points"]').value = getPointsEquivalent(average);
}

// GPA point system
function getPointsEquivalent(grade) {
    if (grade >= 97) return "1.0";
    if (grade >= 94) return "1.25";
    if (grade >= 91) return "1.5";
    if (grade >= 88) return "1.75";
    if (grade >= 85) return "2.0";
    if (grade >= 82) return "2.25";
    if (grade >= 79) return "2.5";
    if (grade >= 76) return "2.75";
    if (grade >= 75) return "3.0";
    return "5.0"; // fail
}

document.querySelectorAll('input[name="prelims"], input[name="midterms"], input[name="prefinals"], input[name="finals"]').forEach(input => {
    input.addEventListener('input', updateFinalGrade);
});
</script>

</body>
</html>
