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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_accountability'])) {
    $student_user_id = intval($_POST['student_user_id']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if ($description !== '') {
        $stmt = $conn->prepare("INSERT INTO accountabilities (student_user_id, description, amount, status, category) VALUES (?, ?, 0, ?, 'document')");
        $stmt->bind_param("iss", $student_user_id, $description, $status);
        $stmt->execute();
        $stmt->close();
        // Redirect with success param for SweetAlert
        header("Location: adding_documents.php?success=add");
        exit;
    } else {
        // Redirect with error param (optional)
        header("Location: adding_documents.php?error=description");
        exit;
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #f9f9f9; display: flex; height: 100vh; }
    .sidebar { width: 220px; background-color: #2c3e50; color: white; padding: 20px; box-sizing: border-box; }
    .sidebar h2 { margin-top: 0; margin-bottom: 20px; font-size: 20px; }
    .sidebar a { color: white; text-decoration: none; margin-bottom: 15px; font-size: 16px; padding: 8px 12px; border-radius: 4px; display: block; }
    .sidebar a:hover { background-color: #34495e; }
    .main-content { flex: 1; padding: 20px; overflow-y: auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #2c3e50; color: white; }
    form { background: white; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    label { display: block; margin-top: 10px; }
    textarea, select { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
    button { margin-top: 15px; padding: 10px 15px; background-color: #3498db; color: white; border: none; cursor: pointer; }
    button:hover { background-color: #2980b9; }
    .user-info { margin-bottom: 20px; }
    .user-info strong { color: #2c3e50; }
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
        <th>Actions</th>
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
            <td>
              <button class="btn btn-sm btn-primary edit-btn"
                      data-id="<?= $row['id'] ?>"
                      data-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>"
                      data-status="<?= $row['status'] ?>"
                      data-bs-toggle="modal"
                      data-bs-target="#editModal">Edit</button>
              <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['id'] ?>">Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">No document accountabilities found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Document Accountability</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="accountability_id" id="edit-id">
          <div class="mb-3">
            <label for="edit-description">Description</label>
            <textarea name="description" id="edit-description" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label for="edit-status">Status</label>
            <select name="status" id="edit-status" class="form-control" required>
              <option value="pending">Pending</option>
              <option value="resolved">Resolved</option>
              <option value="failed">Failed</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <!-- Changed from type=submit to button to handle AJAX -->
          <button type="button" class="btn btn-success" id="saveChangesBtn">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  // Populate edit modal with data
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('edit-id').value = button.getAttribute('data-id');
      document.getElementById('edit-description').value = button.getAttribute('data-description');
      document.getElementById('edit-status').value = button.getAttribute('data-status');
    });
  });

  // Delete confirmation with SweetAlert
  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the accountability.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = `delete_documents.php?id=${id}`;
        }
      });
    });
  });

  // Handle edit form AJAX submission with SweetAlert
  document.getElementById('saveChangesBtn').addEventListener('click', function() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    fetch('edit_documents.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: 'Document accountability updated successfully.',
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          // Close the modal
          const editModalEl = document.getElementById('editModal');
          const modal = bootstrap.Modal.getInstance(editModalEl);
          modal.hide();

          // Reload page or update table as needed
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'Failed to update document accountability.'
        });
      }
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to connect to the server.'
      });
    });
  });

  // SweetAlert for success messages from URL param for add/delete
  const urlParams = new URLSearchParams(window.location.search);
  const success = urlParams.get('success');
  const error = urlParams.get('error');

  if (success === 'add') {
    Swal.fire({
      icon: 'success',
      title: 'Added!',
      text: 'Document accountability added successfully.',
      timer: 2000,
      showConfirmButton: false
    }).then(() => {
      history.replaceState(null, '', window.location.pathname);
    });
  }

  if (success === 'delete') {
    Swal.fire({
      icon: 'success',
      title: 'Deleted!',
      text: 'Document accountability deleted.',
      timer: 2000,
      showConfirmButton: false
    }).then(() => {
      history.replaceState(null, '', window.location.pathname);
    });
  }

  if (error === 'description') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Please enter a description.',
    }).then(() => {
      history.replaceState(null, '', window.location.pathname);
    });
  }
</script>

</body>
</html>
