<?php
session_start();
include 'include/config.php';

// Only treasury can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'treasury') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Get student pending balance helper
function getStudentBalance($conn, $student_user_id) {
    $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS balance FROM accountabilities WHERE student_user_id = ? AND status = 'pending' AND category = 'financial'");
    $stmt->bind_param("i", $student_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $balance = 0;
    if ($row = $result->fetch_assoc()) {
        $balance = $row['balance'];
    }
    $stmt->close();
    return $balance;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_accountability'])) {
    $student_user_id = intval($_POST['student_user_id']);
    $description = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    $status = $_POST['status'];

    $balance = getStudentBalance($conn, $student_user_id);

    if ($balance > 0) {
        if ($description !== '' && $amount > 0) {
            $stmt = $conn->prepare("INSERT INTO accountabilities (student_user_id, description, amount, status, category) VALUES (?, ?, ?, ?, 'financial')");
            $stmt->bind_param("isds", $student_user_id, $description, $amount, $status);
            $stmt->execute();
            $stmt->close();
            $message = "Financial accountability added successfully.";
        } else {
            $error = "Please fill all fields correctly.";
        }
    } else {
        $error = "Cannot add financial accountability: Student has no pending balance.";
    }
}

// Fetch students with role='student' for dropdown
$studentsResult = $conn->query("SELECT id, username FROM users WHERE role = 'student' ORDER BY username ASC");

// Fetch all financial accountabilities
$sql = "SELECT a.id, u.username, a.description, a.amount, a.status, a.created_at 
        FROM accountabilities a
        JOIN users u ON a.student_user_id = u.id
        WHERE a.category = 'financial'
        ORDER BY a.created_at DESC";
$accountabilitiesResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Treasury - Financial Accountabilities</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9;}
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background-color: #2c3e50; color: white; }
form { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
label { display: block; margin-top: 10px; }
input[type=text], input[type=number], select, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
button { margin-top: 15px; padding: 10px 15px; background-color: #1abc9c; color: white; border: none; cursor: pointer; }
button:hover { background-color: #16a085; }
.message { color: green; }
.error { color: red; }
.logout { float: right; }
</style>
</head>
<body>

<h1>Treasury - Financial Accountabilities</h1>
<p>Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (<?= $_SESSION['role'] ?>) <a class="logout" href="?logout=1">Logout</a></p>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <h2>Add Financial Accountability</h2>

    <label for="student_user_id">Select Student:</label>
    <select name="student_user_id" id="student_user_id" required>
        <option value="">-- Select Student --</option>
        <?php while ($student = $studentsResult->fetch_assoc()): ?>
            <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['username']) ?></option>
        <?php endwhile; ?>
    </select>

    <label for="description">Description:</label>
    <textarea name="description" id="description" rows="3" required></textarea>

    <label for="amount">Amount:</label>
    <input type="number" step="0.01" name="amount" id="amount" required min="0">

    <label for="status">Status:</label>
    <select name="status" id="status" required>
        <option value="pending" selected>Pending</option>
        <option value="paid">Paid</option>
        <option value="overdue">Overdue</option>
    </select>

    <button type="submit" name="add_accountability">Add Accountability</button>
</form>

<h2>Financial Accountabilities</h2>
<table>
    <thead>
        <tr>
            <th>Student Username</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($accountabilitiesResult->num_rows > 0): ?>
        <?php while ($row = $accountabilitiesResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= number_format($row['amount'], 2) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No financial accountabilities found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>

