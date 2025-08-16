<?php
session_start();
include 'include/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = '';

// Handle requirement status update via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['requirement_id'], $_POST['new_status'])) {
    $requirementId = (int)$_POST['requirement_id'];
    $newStatus = $_POST['new_status']; // e.g. 'received'

    $stmtUpdate = $conn->prepare("UPDATE admission_requirements SET status=? WHERE id=?");
    $stmtUpdate->bind_param("si", $newStatus, $requirementId);
    if ($stmtUpdate->execute()) {
        $stmtUpdate->close();

        // Check if all requirements for this application are now 'received'
        // First, get the application_id of this requirement
        $stmtAppId = $conn->prepare("SELECT application_id FROM admission_requirements WHERE id=?");
        $stmtAppId->bind_param("i", $requirementId);
        $stmtAppId->execute();
        $resAppId = $stmtAppId->get_result();
        $rowAppId = $resAppId->fetch_assoc();
        $stmtAppId->close();

        $applicationId = $rowAppId['application_id'];

        // Check if any requirements still pending for this application
        $stmtCheck = $conn->prepare("SELECT COUNT(*) as pending_count FROM admission_requirements WHERE application_id=? AND status!='received'");
        $stmtCheck->bind_param("i", $applicationId);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $pendingCount = $resCheck->fetch_assoc()['pending_count'];
        $stmtCheck->close();

        if ($pendingCount == 0) {
            // Update application status to requirements_resolved
            $stmtUpdateApp = $conn->prepare("UPDATE admission_applications SET status='requirements_resolved' WHERE id=?");
            $stmtUpdateApp->bind_param("i", $applicationId);
            $stmtUpdateApp->execute();
            $stmtUpdateApp->close();

            $message = "<script>window.onload = function() { Swal.fire('All Requirements Received', 'Application status updated.', 'success'); }</script>";
        } else {
            $message = "<script>window.onload = function() { Swal.fire('Requirement Updated', 'Requirement status updated.', 'success'); }</script>";
        }
    } else {
        $message = "<script>window.onload = function() { Swal.fire('Error', 'Failed to update requirement status.', 'error'); }</script>";
    }
}

// Fetch all applications with status 'requirements_sent' or 'requirements_resolved' (optional)
$sqlApps = "SELECT * FROM admission_applications WHERE status='requirements_sent' OR status='requirements_resolved' ORDER BY submitted_at DESC";
$resultApps = $conn->query($sqlApps);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Requirements Admission</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        button.status-btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 4px;
        }
        button.received { background-color: #28a745; }
        button.pending { background-color: #dc3545; }
        .applicant-section { margin-bottom: 40px; border: 1px solid #ccc; padding: 15px; border-radius: 8px; }
    </style>
</head>
<body>

<h1>Requirements Admission</h1>
<?= $message ?>

<?php if ($resultApps && $resultApps->num_rows > 0): ?>
    <?php while ($applicant = $resultApps->fetch_assoc()): ?>
        <div class="applicant-section">
            <h2>Applicant: <?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?> (Status: <?= htmlspecialchars($applicant['status']) ?>)</h2>
            <p><strong>Desired Course:</strong> <?= htmlspecialchars($applicant['desired_course']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($applicant['email']) ?></p>

            <?php
            $appId = $applicant['id'];
            // Fetch requirements for this applicant
            $stmtReq = $conn->prepare("SELECT * FROM admission_requirements WHERE application_id=?");
            $stmtReq->bind_param("i", $appId);
            $stmtReq->execute();
            $resultReq = $stmtReq->get_result();
            ?>

            <?php if ($resultReq && $resultReq->num_rows > 0): ?>
                <table>
                    <thead>
                    <tr>
                        <th>Requirement</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($req = $resultReq->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['requirement_name']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($req['status'])) ?></td>
                            <td>
                                <?php if ($req['status'] !== 'received'): ?>
                                    <form method="POST" style="margin:0;">
                                        <input type="hidden" name="requirement_id" value="<?= $req['id'] ?>">
                                        <input type="hidden" name="new_status" value="received">
                                        <button type="submit" class="status-btn received" onclick="return confirm('Mark this requirement as received?')">Mark as Received</button>
                                    </form>
                                <?php else: ?>
                                    <button class="status-btn received" disabled>Received</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No requirements found for this applicant.</p>
            <?php endif; ?>
            <?php $stmtReq->close(); ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No applications with requirements sent.</p>
<?php endif; ?>

</body>
</html>
