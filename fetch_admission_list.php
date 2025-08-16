<?php
// fetch_applicant_details.php
include 'include/config.php';  // Adjust path as needed

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color:red;'>Invalid applicant ID.</p>";
    exit;
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM admission_applications WHERE id=?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<p style='color:red;'>Database error.</p>";
    exit;
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();
$stmt->close();

if (!$applicant) {
    echo "<p style='color:red;'>Applicant not found.</p>";
    exit;
}
?>

<style>
  body {
    font-family: Arial, sans-serif;
    color: #333;
  }
  h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #222;
  }
  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 12px; /* space between rows */
  }
  td:first-child {
    font-weight: 600;
    width: 40%;
    padding-left: 8px;
    color: #222;
    vertical-align: middle;
    text-transform: capitalize;
  }
  td:last-child {
    width: 60%;
    padding: 0;
  }
  .input-style {
    width: 98%;
    padding: 8px 12px;
    font-size: 14px;
    border: 1.5px solid #ccc;
    border-radius: 4px;
    background: #fff;
    color: #222;
    box-sizing: border-box;
  }
</style>

<h2>Applicant Details</h2>
<table>
  <?php foreach ($applicant as $key => $value):
      if ($key === 'id') continue;  // Skip the id field
  ?>
    <tr>
      <td><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></td>
      <td><input class="input-style" type="text" value="<?= htmlspecialchars($value) ?>" readonly></td>
    </tr>
  <?php endforeach; ?>
</table>
