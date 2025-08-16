<?php
session_start();
include 'include/config.php';

// Only registrar can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'registrar') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_accountability'])) {
    $student_user_id = intval($_POST['student_user_id']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if ($description !== '') {
        // amount is 0 for document-related accountabilities
        $stmt = $conn->prepare("INSERT INTO accountabilities (student_user_id, description, amount, status, category) VALUES (?, ?, 0, ?, 'document')");
        $stmt->bind_param("iss", $student_user_id, $description, $status);
        $stmt->execute();
        $stmt->close();
        $message = "Document accountability added successfully.";
    } else {
        $error = "Please enter a description.";
    }
}

// Fetch students with role='student'
$studentsResult = $conn->query("SELECT id, username FROM users WHERE role = 'student' ORDER BY username ASC");

// Fetch all document accountabilities
$sql = "SELECT a.id, u.username, a.description, a.status, a.created_at 
        FROM accountabilities a
        JOIN users u ON a.student_user_id = u.id
        WHERE a.category = 'document'
        ORDER BY a.created_at DESC";
$accountabilitiesResult = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Registrar - Document Accountabilities</title>
<style>
  /* Layout */
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    display: flex;
    height: 100vh;
  }
  /* Sidebar */
  .sidebar {
    width: 220px;
    background-color: #2c3e50;
    color: white;
    display: flex;
    flex-direction: column;
    padding: 20px;
    box-sizing: border-box;
  }
  .sidebar h2 {
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 20px;
  }
  .sidebar a {
    color: white;
    text-decoration: none;
    margin-bottom: 15px;
    font-size: 16px;
    padding: 8px 12px;
    border-radius: 4px;
    display: block;
  }
  .sidebar a:hover {
    background-color: #34495e;
  }
  /* Main content */
  .main-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
  }

  /* Table */
  table {
    border-collapse: collapse;
    width: 100%;
  }
  th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
  }
  th {
    background-color: #2c3e50;
    color: white;
  }

  /* Form */
  form {
    background: white;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
  }
  label {
    display: block;
    margin-top: 10px;
  }
  textarea, select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    box-sizing: border-box;
  }
  button {
    margin-top: 15px;
    padding: 10px 15px;
    background-color: #3498db;
    color: white;
    border: none;
    cursor: pointer;
  }
  button:hover {
    background-color: #2980b9;
  }

  /* Messages */
  .message {
    color: green;
  }
  .error {
    color: red;
  }

  /* Logged in info */
  .user-info {
    margin-bottom: 20px;
  }
  .user-info strong {
    color: #2c3e50;
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

<div class="main-content">
  <h1>Registrar - Document Accountabilities</h1>
  <p class="user-info">Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (<?= $_SESSION['role'] ?>)</p>

  <?php if ($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" action="">
      <h2>Add Document Accountability</h2>

      <label for="student_user_id">Select Student:</label>
      <select name="student_user_id" id="student_user_id" required>
          <option value="">-- Select Student --</option>
          <?php while ($student = $studentsResult->fetch_assoc()): ?>
              <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['username']) ?></option>
          <?php endwhile; ?>
      </select>

      <label for="description">Description (e.g. Missing Document):</label>
      <textarea name="description" id="description" rows="3" required></textarea>

      <label for="status">Status:</label>
      <select name="status" id="status" required>
          <option value="pending" selected>Pending</option>
          <option value="resolved">Resolved</option>
          <option value="failed">Failed</option>
      </select>

      <button type="submit" name="add_accountability">Add Accountability</button>
  </form>

  <h2>Document Accountabilities</h2>
  <table>
      <thead>
          <tr>
              <th>Student Username</th>
              <th>Description</th>
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
                  <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                  <td><?= $row['created_at'] ?></td>
              </tr>
          <?php endwhile; ?>
      <?php else: ?>
          <tr><td colspan="4">No document accountabilities found.</td></tr>
      <?php endif; ?>
      </tbody>
  </table>
</div>

</body>
</html>
