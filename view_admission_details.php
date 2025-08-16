<?php
include 'include/config.php';

if (!isset($_GET['id'])) {
    echo "No application ID provided.";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM admission_applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if (!$row = $res->fetch_assoc()) {
    echo "Application not found.";
    exit;
}

echo '<table class="table table-bordered">';
foreach ($row as $key => $val) {
    echo '<tr>';
    echo '<th>' . ucwords(str_replace('_', ' ', $key)) . '</th>';
    echo '<td>' . htmlspecialchars($val) . '</td>';
    echo '</tr>';
}
echo '</table>';
