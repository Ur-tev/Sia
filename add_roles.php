<?php
session_start();
include 'include/config.php';

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php.php");
    exit;
}

$error = '';
$success = '';

// Get latest student username number
$latest_number = 0;
$current_year = date('Y');
$res = $conn->query("SELECT username FROM users WHERE role='student' AND username LIKE '$current_year-%' ORDER BY id DESC LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $parts = explode('-', $row['username']);
    if (isset($parts[1]) && is_numeric($parts[1])) {
        $latest_number = (int)$parts[1];
    }
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $new_username = trim($_POST['new_username']);
    $new_password = trim($_POST['new_password']);
    $new_role = trim($_POST['new_role']);

    if (!empty($new_password) && !empty($new_role)) {

        // If student, auto-generate username server-side too
        if ($new_role === 'student') {
            $res2 = $conn->query("SELECT username FROM users WHERE role='student' AND username LIKE '$current_year-%' ORDER BY id DESC LIMIT 1");
            $latest_num_server = 0;
            if ($res2 && $row2 = $res2->fetch_assoc()) {
                $parts2 = explode('-', $row2['username']);
                if (isset($parts2[1]) && is_numeric($parts2[1])) {
                    $latest_num_server = (int)$parts2[1];
                }
            }
            $new_username = $current_year . '-' . ($latest_num_server + 1);
        }

        if (!empty($new_username)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $new_username, $hashed_password, $new_role);
            if ($stmt->execute()) {
                $success = "User '$new_username' with role '$new_role' added successfully.";
            } else {
                $error = "Error adding user: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Please fill in all fields for new user.";
    }
}

// Handle Edit User
if (isset($_POST['edit_user'])) {
    $edit_id = $_POST['edit_id'];
    $edit_username = trim($_POST['edit_username']);
    $edit_password = trim($_POST['edit_password']);
    $edit_role = trim($_POST['edit_role']);

    if (!empty($edit_username) && !empty($edit_role)) {
        if (!empty($edit_password)) {
            $hashed_password = password_hash($edit_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $edit_username, $hashed_password, $edit_role, $edit_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
            $stmt->bind_param("ssi", $edit_username, $edit_role, $edit_id);
        }
        if ($stmt->execute()) {
            $success = "User updated successfully.";
        } else {
            $error = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Username and role are required.";
    }
}

// Handle Delete User
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success = "User deleted successfully.";
    } else {
        $error = "Error deleting user.";
    }
    $stmt->close();
}

// Fetch all users
$users = [];
$result = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Roles</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f4f4; display: flex; }
        .sidebar { width: 220px; background: #2c3e50; color: #fff; height: 100vh; padding-top: 20px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; }
        .sidebar a:hover { background: #34495e; }
        .main { margin-left: 220px; padding: 20px; flex: 1; }
        .main .container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 20px; }
        input, select, button { width: 100%; padding: 10px; margin: 6px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #3498db; color: white; border: none; cursor: pointer; }
        button:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; background: white; border-radius: 8px; overflow: hidden; }
        table th, table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #3498db; color: white; }
        tr:nth-child(even) { background-color: #fdfdfd; }
        tr:hover { background-color: #f1f7ff; }
        .action-btns button { border: none; padding: 6px 10px; margin: 0 3px; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .action-btns button:first-child { background-color: #4cafef; color: white; }
        .action-btns button:last-child { background-color: #f44336; color: white; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; margin: 5% auto; padding: 20px; border-radius: 10px; width: 420px; max-width: 90%; position: relative; }
        .close { position: absolute; right: 15px; top: 12px; font-size: 22px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">🏠 Admin Dashboard</a>
    <a href="#add-role">➕ Add Roles</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
    <div class="container" id="add-role">
        <h2>Create New User</h2>
        <form method="post">
            <input type="text" name="new_username" placeholder="Username / Student ID" required>
            <input type="password" name="new_password" placeholder="Password" required>
            <select name="new_role" required>
                <option value="">--Select Role--</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="treasury">Treasury</option>
                <option value="registrar">Registrar</option>
                <option value="department-head">Department Head</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>

    <div class="container">
        <h2>📋 List of Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td class="action-btns">
                    <button onclick="openEditModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= htmlspecialchars($user['role']) ?>')">✏️ Edit</button>
                    <button onclick="confirmDelete(<?= $user['id'] ?>)">🗑️ Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit User</h2>
        <form method="post">
            <input type="hidden" name="edit_id" id="edit_id">
            <label>Username:</label>
            <input type="text" name="edit_username" id="edit_username" required>
            <label>Password:</label>
            <input type="password" name="edit_password" placeholder="Leave blank to keep current password">
            <label>Role:</label>
            <select name="edit_role" id="edit_role" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="treasury">Treasury</option>
                <option value="registrar">Registrar</option>
                <option value="department-head">Department Head</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="edit_user">💾 Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, username, role) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
    document.getElementById('editModal').style.display = 'block';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the user.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#3498db',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?delete_id=' + id;
        }
    });
}

// Auto-generate student username in form
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.querySelector('select[name="new_role"]');
    const usernameInput = document.querySelector('input[name="new_username"]');
    const latestNumber = <?= json_encode($latest_number) ?>;
    const currentYear = <?= json_encode($current_year) ?>;

    roleSelect.addEventListener('change', function () {
        if (this.value === 'student') {
            usernameInput.value = currentYear + '-' + (latestNumber + 1);
            usernameInput.readOnly = true;
        } else {
            usernameInput.value = '';
            usernameInput.readOnly = false;
        }
    });

    // Trigger SweetAlert for success/error
    <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: <?= json_encode($success) ?>,
            confirmButtonColor: '#3498db'
        });
    <?php endif; ?>

    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: <?= json_encode($error) ?>,
            confirmButtonColor: '#e74c3c'
        });
    <?php endif; ?>
});
</script>

</body>
</html>
