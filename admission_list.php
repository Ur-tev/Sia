<?php
session_start();
include 'include/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

function sendEmail($to, $name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'stevegramatica2@gmail.com';
        $mail->Password = 'tghwnvywaozpbnpr'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('stevegramatica2@gmail.com', 'Admission Office');
        $mail->addAddress($to, $name);

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<p style='color:red;'>Mailer Error: {$mail->ErrorInfo}</p>";
        return false;
    }
}

$message = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $applicationId = (int)$_GET['id'];
    $action = $_GET['action'];

    // Get applicant data
    $sql_get = "SELECT * FROM admission_applications WHERE id=?";
    $stmt_get = $conn->prepare($sql_get);
    $stmt_get->bind_param("i", $applicationId);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    $applicant = $result->fetch_assoc();
    $stmt_get->close();

    if ($applicant) {
        $fullName = $applicant['first_name'] . ' ' . $applicant['last_name'];
        $email = $applicant['email'];
        $lastName = $applicant['last_name'];

        if ($action === 'approve') {
            if ($applicant['status'] === 'pending') {
                // Update status to requirements_sent and insert requirement records
                $sql_update = "UPDATE admission_applications SET status='requirements_sent' WHERE id=?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("i", $applicationId);
                $stmt->execute();
                $stmt->close();

                $requirements = [
                    'Birth Certificate',
                    'Transcript of Records',
                    'Good Moral Certificate',
                    '2x2 Photo'
                ];

                $stmt_req = $conn->prepare("INSERT INTO admission_requirements (application_id, requirement_name, status) VALUES (?, ?, 'pending')");
                foreach ($requirements as $req) {
                    $stmt_req->bind_param("is", $applicationId, $req);
                    $stmt_req->execute();
                }
                $stmt_req->close();

                $subject = "Admission Requirements";
                $body = "Dear $fullName,\n\nThank you for your application.\nPlease submit the following requirements:\n\n- Birth Certificate\n- Transcript of Records\n- Good Moral Certificate\n- 2x2 Photo\n\nOnce received, your application will be reviewed.\n\nRegards,\nAdmission Office";

                if (sendEmail($email, $fullName, $subject, $body)) {
                    $message = "<script>window.onload = function() { Swal.fire('Requirements Sent', 'Email sent successfully.', 'success'); }</script>";
                } else {
                    $message = "<script>window.onload = function() { Swal.fire('Email Failed', 'Could not send email.', 'error'); }</script>";
                }
            } elseif ($applicant['status'] === 'requirements_resolved') {
                // Final approval - create user and send credentials
                $year = date('Y');
                $desiredCourse = $applicant['desired_course'];
                $passwordPlain = $lastName;
                $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

                // Get last username with pattern YEAR-####
                $likePattern = $year . '-%';
                $stmtMax = $conn->prepare("SELECT username FROM users WHERE username LIKE ? ORDER BY username DESC LIMIT 1");
                $stmtMax->bind_param("s", $likePattern);
                $stmtMax->execute();
                $resMax = $stmtMax->get_result();
                $lastId = $resMax->fetch_assoc();
                $stmtMax->close();

                if ($lastId) {
                    // Extract number part and increment
                    $parts = explode('-', $lastId['username']);
                    $lastNumber = isset($parts[1]) ? (int)$parts[1] : 0;
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                // Pad number with leading zeros (e.g. 0001)
                $studentId = $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $stmtUser = $conn->prepare("INSERT INTO users (username, password, role, created_at, email, course) VALUES (?, ?, 'student', NOW(), ?, ?)");
                $stmtUser->bind_param("ssss", $studentId, $passwordHash, $email, $desiredCourse);
                if ($stmtUser->execute()) {
                    $stmtUser->close();

                    // Update application status to approved
                    $stmt = $conn->prepare("UPDATE admission_applications SET status='approved' WHERE id=?");
                    $stmt->bind_param("i", $applicationId);
                    $stmt->execute();
                    $stmt->close();

                    $subject = "Admission Approved – Student Credentials";
                    $body = "Dear $fullName,\n\nCongratulations! Your application has been approved.\n\nStudent ID: $studentId\nPassword: $passwordPlain\n\nPlease log in and change your password immediately.\n\nRegards,\nRegistrar Office";

                    if (sendEmail($email, $fullName, $subject, $body)) {
                        $message = "<script>window.onload = function() { Swal.fire('Approved', 'Student ID and password sent.', 'success'); }</script>";
                    } else {
                        $message = "<script>window.onload = function() { Swal.fire('Partial Success', 'User created but email failed.', 'warning'); }</script>";
                    }
                } else {
                    $message = "<script>window.onload = function() { Swal.fire('Error', 'User creation failed.', 'error'); }</script>";
                }
            }
        } elseif ($action === 'reject') {
            $subject = "Admission Rejected";
            $body = "Dear $fullName,\n\nWe regret to inform you that your application has been rejected.\n\nRegards,\nAdmission Office";
            if (sendEmail($email, $fullName, $subject, $body)) {
                $stmt_del = $conn->prepare("DELETE FROM admission_applications WHERE id=?");
                $stmt_del->bind_param("i", $applicationId);
                $stmt_del->execute();
                $stmt_del->close();
                $message = "<script>window.onload = function() { Swal.fire('Rejected', 'Application deleted and email sent.', 'info'); }</script>";
            } else {
                $message = "<script>window.onload = function() { Swal.fire('Email Failed', 'Could not send rejection email.', 'error'); }</script>";
            }
        }
    } else {
        $message = "<script>window.onload = function() { Swal.fire('Error', 'Applicant not found.', 'error'); }</script>";
    }
}

// Fetch all applications ordered by submission date
$result = $conn->query("SELECT * FROM admission_applications ORDER BY submitted_at DESC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admission List</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .sidebar {
            width: 200px; background: #333; height: 100vh; color: white; float: left; padding: 15px;
        }
        .sidebar h2 {
            margin-top: 0; margin-bottom: 20px;
        }
        .sidebar a {
            color: white; text-decoration: none; display: block; margin: 8px 0;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .main-content {
            margin-left: 220px; padding: 20px;
        }
        table {
            border-collapse: collapse; width: 100%;
        }
        th, td {
            border: 1px solid #ccc; padding: 8px; text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button, a.action-link {
            cursor: pointer; color: blue; background: none; border: none; text-decoration: underline; padding: 0; font-size: 1em;
        }
        button.viewBtn {
            color: green; font-weight: bold;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Menu</h2>
    <a href="registrar_dashboard.php">Registrar Dashboard</a>
    <a href="admission_list.php">Admission Details</a>
    <a href="requirements_admission.php">Requirements Admission</a>
    <a href="adding_documents.php">Adding Documents</a>
    <a href="registrar_grades.php">Finalize Grades</a>
    <a href="registrar_regular.php">Registrar of Regular</a>
    <a href="?logout=1">Logout</a>
</div>

<div class="main-content">
    <h1>Admission List</h1>
    <?= $message ?>
    <table>
        <thead>
        <tr>
            <th>ID</th><th>First</th><th>Middle</th><th>Last</th><th>Course</th><th>Gender</th>
            <th>Mobile</th><th>Landline</th><th>DOB</th><th>Address</th><th>Email</th><th>Status</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['middle_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['desired_course']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['mobile_no']) ?></td>
                    <td><?= htmlspecialchars($row['landline']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($row['complete_address']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <button class="viewBtn" data-id="<?= $row['id'] ?>">View</button>
                        <?php if ($row['status'] === 'pending'): ?>
                            | <a href="#" class="action-link approve-link" data-id="<?= $row['id'] ?>" data-step="1">Send Requirements</a>
                            | <a href="#" class="action-link reject-link" data-id="<?= $row['id'] ?>">Reject</a>
                        <?php elseif ($row['status'] === 'requirements_sent'): ?>
                            | Waiting for requirements to be resolved
                        <?php elseif ($row['status'] === 'requirements_resolved'): ?>
                            | <a href="#" class="action-link approve-link" data-id="<?= $row['id'] ?>" data-step="2">Approve & Send Credentials</a>
                            | <a href="#" class="action-link reject-link" data-id="<?= $row['id'] ?>">Reject</a>
                        <?php elseif ($row['status'] === 'approved'): ?>
                            | Approved
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="13">No records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Confirmations before actions
document.querySelectorAll('.approve-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        const step = this.getAttribute('data-step');
        let msg = step === '1' ? "Send requirements email to applicant?" : "Approve application and send student credentials?";
        Swal.fire({
            title: 'Confirm',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `admission_list.php?action=approve&id=${id}`;
            }
        });
    });
});

document.querySelectorAll('.reject-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        Swal.fire({
            title: 'Reject Application',
            text: "Are you sure you want to reject this application? It will be deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Reject',
            confirmButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `admission_list.php?action=reject&id=${id}`;
            }
        });
    });
});

// Optional: implement view button functionality if needed
document.querySelectorAll('.viewBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        alert(`View details for Application ID: ${id}`);
        // You can implement modal or redirection here
    });
});
</script>

</body>
</html>
